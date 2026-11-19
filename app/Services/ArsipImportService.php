<?php

namespace App\Services;

use App\Models\Arsip;
use App\Models\AuditLog;
use App\Models\Jra;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Impor data arsip dari baris spreadsheet.
 *
 * Dua format yang dikenali (dideteksi dari judul kolom):
 *  1. Template Simarsip : Nomor Surat | Tanggal Surat | Pengirim | Penerima | Perihal | Kode JRA | Kode Rak | Baris/Tingkat | Nomor Boks
 *  2. "Daftar Isi Berkas": kolom URAIAN INFORMASI ARSIP (nomor surat, pengirim, penerima, perihal
 *     diambil dari kalimat uraian), opsional kolom Tanggal/Tahun, Kode Klasifikasi, dan lokasi.
 */
class ArsipImportService
{
    private const HEADERS = [
        'kode_surat'  => ['kodesurat', 'nomorsurat', 'nosurat', 'nomornaskah', 'nomornaskahdinas'],
        'tanggal'     => ['tanggalsurat', 'tanggal', 'tglsurat', 'tgl', 'tanggalnaskah'],
        'tahun'       => ['tahun', 'tahunsurat'],
        'pengirim'    => ['pengirim', 'asalsurat', 'dari'],
        'penerima'    => ['penerima', 'tujuan', 'kepada', 'tujuansurat'],
        'perihal'     => ['perihal', 'hal', 'isiringkas'],
        'uraian'      => ['uraianinformasiarsip', 'uraianinformasi', 'uraian'],
        'klasifikasi' => ['kodejra', 'kodeklasifikasi', 'klasifikasi', 'jra', 'jenisarsip'],
        'kode_rak'    => ['koderak', 'rak'],
        'baris'       => ['baristingkat', 'baris', 'tingkat'],
        'boks'        => ['nomorboks', 'noboks', 'boks'],
    ];

    private const BULAN = [
        'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4, 'mei' => 5, 'juni' => 6,
        'juli' => 7, 'agustus' => 8, 'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'jun' => 6, 'jul' => 7, 'agu' => 8, 'agt' => 8,
        'sep' => 9, 'sept' => 9, 'okt' => 10, 'nov' => 11, 'des' => 12,
    ];

    /**
     * @param  array<int,array<int,string>>  $rows
     * @param  array{kode_rak?:?string,baris_tingkat?:?string,nomor_boks?:?string}  $lokasi
     */
    public static function import(array $rows, array $lokasi, $user): array
    {
        [$headerIdx, $map] = self::detectHeader($rows);

        $res = ['total' => 0, 'berhasil' => 0, 'duplikat' => 0, 'gagal' => 0, 'tanpa_jra' => 0, 'errors' => []];

        $defRak   = trim((string) ($lokasi['kode_rak'] ?? '')) ?: '-';
        $defBaris = trim((string) ($lokasi['baris_tingkat'] ?? '')) ?: '-';
        $defBoks  = trim((string) ($lokasi['nomor_boks'] ?? '')) ?: '-';

        // Kunci arsip yang sudah ada (untuk dilewati)
        $existing = [];
        foreach (Arsip::where('is_deleted', false)->get(['kode_surat', 'tanggal_surat', 'pengirim', 'perihal']) as $a) {
            $existing[self::key($a->kode_surat, $a->tanggal_surat->toDateString(), $a->pengirim, $a->perihal)] = true;
        }

        $jraByKode = Jra::all()->keyBy(fn ($j) => strtoupper($j->kode));
        $jraBySeries = Jra::all()->keyBy(fn ($j) => mb_strtolower(trim($j->series)));

        $creator = $user ? "{$user->name} ({$user->jabatan})" : 'Petugas (Administrator)';
        $seq = (int) Arsip::max('id') + 1;

        DB::transaction(function () use ($rows, $headerIdx, $map, &$res, &$existing, $jraByKode, $jraBySeries, $defRak, $defBaris, $defBoks, $creator, $user, &$seq) {
            for ($i = $headerIdx + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $get = fn (string $k) => isset($map[$k]) ? trim((string) ($row[$map[$k]] ?? '')) : '';

                if (!array_filter($row, fn ($v) => trim((string) $v) !== '')) {
                    continue;
                }

                $nomor    = $get('kode_surat');
                $perihal  = $get('perihal');
                $pengirim = $get('pengirim');
                $penerima = $get('penerima');
                $tglRaw   = $get('tanggal');
                $uraian   = $get('uraian');

                if (stripos($nomor, 'CONTOH') === 0) {
                    continue; // baris contoh pada template
                }

                $res['total']++;
                $baris = $i + 1;

                if ($uraian !== '') {
                    $p = self::parseUraian($uraian);
                    $nomor    = $nomor !== '' ? $nomor : $p['nomor'];
                    $pengirim = $pengirim !== '' ? $pengirim : $p['pengirim'];
                    $penerima = $penerima !== '' ? $penerima : $p['penerima'];
                    $perihal  = $perihal !== '' ? $perihal : ($p['perihal'] ?: $uraian);
                    $tglRaw   = $tglRaw !== '' ? $tglRaw : $p['tanggal'];
                }

                if ($perihal === '') {
                    $res['gagal']++;
                    $res['errors'][] = "Baris {$baris}: perihal/uraian kosong.";
                    continue;
                }

                $tanggal = self::parseDate($tglRaw);
                if (!$tanggal) {
                    $tahunCol = (int) $get('tahun');
                    if ($tahunCol >= 1990 && $tahunCol <= 2100) {
                        $tanggal = Carbon::create($tahunCol, 1, 1);
                    }
                }
                if (!$tanggal) {
                    $res['gagal']++;
                    $res['errors'][] = "Baris {$baris}: tanggal surat tidak terbaca (\"{$tglRaw}\"). Gunakan format dd/mm/yyyy.";
                    continue;
                }

                $kodeSurat = $nomor !== '' ? $nomor : 'S/N-' . $tanggal->format('Ymd');
                $pengirim  = $pengirim !== '' ? $pengirim : '-';
                $penerima  = $penerima !== '' ? $penerima : '-';

                $k = self::key($kodeSurat, $tanggal->toDateString(), $pengirim, $nomor === '' ? $perihal : '');
                $kExisting = self::key($kodeSurat, $tanggal->toDateString(), $pengirim, $perihal);
                if (isset($existing[$k]) || isset($existing[$kExisting])) {
                    $res['duplikat']++;
                    continue;
                }
                $existing[$k] = true;
                $existing[$kExisting] = true;

                // JRA (opsional)
                $klas = $get('klasifikasi');
                $jra = null;
                if ($klas !== '') {
                    if (preg_match('/\b([A-D]\.\d{3})\b/i', $klas, $m)) {
                        $jra = $jraByKode[strtoupper($m[1])] ?? null;
                    }
                    $jra ??= $jraBySeries[mb_strtolower($klas)] ?? null;
                }

                if ($jra) {
                    $ret = RetensiService::hitung($jra, $tanggal);
                    $status = $ret['status_arsip'];
                    $kriteria = $ret['kriteria_akhir'];
                    $tglInaktif = $ret['tanggal_inaktif'];
                    $tglAkhir = $ret['tanggal_akhir_retensi'];
                    $kodeKlas = $jra->label;
                } else {
                    $res['tanpa_jra']++;
                    $status = 'Aktif';
                    $kriteria = 'Permanen';
                    $tglInaktif = $tglAkhir = null;
                    $kodeKlas = $klas !== '' ? mb_substr($klas, 0, 250) : null;
                }

                $rak   = $get('kode_rak') ?: $defRak;
                $brs   = $get('baris') ?: $defBaris;
                $boks  = $get('boks') ?: $defBoks;
                $lokasiKosong = ($rak === '-' && $brs === '-' && $boks === '-');

                do {
                    $year = $tanggal->year;
                    $nomorReg = sprintf('REG-%d-%s-%04d', $year, $tanggal->format('m'), $seq);
                    $uuid = sprintf('ARS-%d-%05d-KU%d', $year, $seq, rand(1, 9));
                    $seq++;
                } while (Arsip::where('nomor_registrasi', $nomorReg)->orWhere('uuid', $uuid)->exists());

                try {
                    $arsip = Arsip::create([
                        'uuid' => $uuid,
                        'nomor_registrasi' => $nomorReg,
                        'nomor_agenda' => sprintf('#%04d/AG/%d', $seq - 1, $year),
                        'kode_surat' => mb_substr($kodeSurat, 0, 250),
                        'kode_klasifikasi' => $kodeKlas,
                        'jra_id' => $jra?->id,
                        'tanggal_inaktif' => $tglInaktif,
                        'tanggal_akhir_retensi' => $tglAkhir,
                        'perihal' => $perihal,
                        'tahun_surat' => $year,
                        'tanggal_surat' => $tanggal->toDateString(),
                        'pengirim' => mb_substr($pengirim, 0, 250),
                        'penerima' => mb_substr($penerima, 0, 250),
                        'status_arsip' => $status,
                        'kriteria_akhir' => $kriteria,
                        'depot_penyimpanan' => 'Data Arsip Dinkes',
                        'kode_rak' => mb_substr($rak, 0, 250),
                        'baris_tingkat' => mb_substr($brs, 0, 250),
                        'nomor_boks' => mb_substr($boks, 0, 250),
                        'lokasi_status' => $lokasiKosong ? 'Lokasi Dikosongkan' : 'Tersimpan',
                        'created_by' => $creator,
                    ]);

                    AuditLog::create([
                        'referensi_audit' => '#' . str_replace('-', '', $nomorReg),
                        'aksi' => 'CREATE',
                        'arsip_id' => $arsip->id,
                        'kode_surat' => $arsip->kode_surat,
                        'perihal' => $arsip->perihal,
                        'actor_name' => $user ? $user->name : 'Petugas Sistem',
                        'actor_nip' => $user ? $user->nip : '-',
                        'actor_role' => $user ? $user->role : 'admin',
                        'detail' => 'Impor Excel: penambahan arsip dengan registrasi ' . $nomorReg,
                    ]);

                    $res['berhasil']++;
                } catch (\Throwable $e) {
                    $res['gagal']++;
                    $res['errors'][] = "Baris {$baris}: gagal disimpan ({$e->getMessage()}).";
                }
            }
        });

        return $res;
    }

    /** @return array{0:int,1:array<string,int>} */
    private static function detectHeader(array $rows): array
    {
        foreach (array_slice($rows, 0, 25, true) as $idx => $row) {
            $map = [];
            foreach ($row as $col => $label) {
                $norm = preg_replace('/[^a-z0-9]/', '', mb_strtolower((string) $label));
                if ($norm === '') {
                    continue;
                }
                foreach (self::HEADERS as $key => $aliases) {
                    if (!isset($map[$key]) && in_array($norm, $aliases, true)) {
                        $map[$key] = $col;
                    }
                }
            }
            if (isset($map['perihal']) || isset($map['uraian'])) {
                if (count($map) >= 2 || isset($map['uraian'])) {
                    return [$idx, $map];
                }
            }
        }

        throw new RuntimeException('Judul kolom tidak dikenali. Gunakan template Excel dari tombol "Unduh template Excel", atau pastikan ada kolom "Perihal" atau "Uraian Informasi Arsip".');
    }

    private static function key(?string $kode, string $tgl, ?string $pengirim, ?string $perihal): string
    {
        return mb_strtolower(trim((string) $kode)) . '|' . $tgl . '|' . mb_strtolower(trim((string) $pengirim)) . '|' . mb_strtolower(trim((string) $perihal));
    }

    public static function parseDate(string $v): ?Carbon
    {
        $v = trim($v);
        if ($v === '') {
            return null;
        }

        // Serial tanggal Excel
        if (preg_match('/^\d{5}(\.\d+)?$/', $v) && (float) $v > 20000 && (float) $v < 80000) {
            return Carbon::create(1899, 12, 30)->addDays((int) floor((float) $v));
        }
        if (preg_match('/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})/', $v, $m) && checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            return Carbon::create((int) $m[1], (int) $m[2], (int) $m[3]);
        }
        if (preg_match('/^(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{4})/', $v, $m) && checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
            return Carbon::create((int) $m[3], (int) $m[2], (int) $m[1]);
        }
        if (preg_match('/(\d{1,2})\s+([A-Za-z]+)\.?\s+(\d{4})/u', $v, $m)) {
            $bln = self::BULAN[mb_strtolower($m[2])] ?? null;
            if ($bln && checkdate($bln, (int) $m[1], (int) $m[3])) {
                return Carbon::create((int) $m[3], $bln, (int) $m[1]);
            }
        }

        return null;
    }

    /**
     * Mengurai kalimat uraian "Daftar Isi Berkas" secara heuristik berdasarkan kata kunci:
     * nomor/no., tanggal, dari, kepada/ditujukan kepada, perihal/hal/tentang.
     */
    public static function parseUraian(string $u): array
    {
        $u = trim(preg_replace('/\s+/u', ' ', $u));
        $stop = '(?:nomor|no\.|tanggal|tgl\.?|perihal|hal|tentang|kepada|ditujukan|dari|pengirim|yth)';
        $out = ['nomor' => '', 'tanggal' => '', 'pengirim' => '', 'penerima' => '', 'perihal' => ''];

        if (preg_match('/\b(?:nomor|no\b\.?)\s*:?\s*([^\s,;]*\/[^\s,;]+)/iu', $u, $m)) {
            $out['nomor'] = rtrim($m[1], '.');
        } elseif (preg_match('/\b(?:nomor|no\b\.?)\s*:?\s*([^\s,;]+)/iu', $u, $m)) {
            $out['nomor'] = rtrim($m[1], '.');
        }

        if (preg_match('/\b(?:tanggal|tgl\.?)\s*:?\s*(\d{1,2}[\s\/.-]+(?:[A-Za-z]+|\d{1,2})[\s\/.-]+\d{4})/iu', $u, $m)) {
            $out['tanggal'] = $m[1];
        }

        if (preg_match('/\b(?:perihal|hal|tentang)\s*:?\s*(.+?)(?=\s*,?\s*\b' . $stop . '\b|$)/iu', $u, $m)) {
            $out['perihal'] = trim($m[1], " ,;:.");
        }
        if (preg_match('/\b(?:dari|pengirim)\s*:?\s*(.+?)(?=\s*,?\s*\b' . $stop . '\b|$)/iu', $u, $m)) {
            $out['pengirim'] = trim($m[1], " ,;:.");
        }
        if (preg_match('/\b(?:ditujukan\s+kepada|kepada|yth\.?)\s*:?\s*(.+?)(?=\s*,?\s*\b' . $stop . '\b|$)/iu', $u, $m)) {
            $out['penerima'] = trim($m[1], " ,;:.");
        }

        return $out;
    }
}
