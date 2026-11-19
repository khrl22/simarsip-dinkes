<?php

namespace App\Services;

use App\Models\Jra;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Menghitung status Aktif / Inaktif / jatuh tempo Musnah secara otomatis
 * berdasarkan Jadwal Retensi Arsip (Perwal Kota Bandung No. 70 Tahun 2020).
 *
 * Aturan:
 *  - Masa AKTIF   : dihitung dari tanggal dasar selama `aktif_tahun`.
 *  - Masa INAKTIF : dihitung setelah masa aktif selama `inaktif_tahun`.
 *  - Setelah masa inaktif habis, nasib akhir mengikuti kolom keterangan JRA:
 *      Musnah              -> jatuh tempo musnah
 *      Permanen            -> diserahkan ke arsip statis (tidak dimusnahkan)
 *      Dinilai Kembali     -> wajib dinilai ulang (tidak otomatis dimusnahkan)
 *      Berkas Perseorangan -> dimasukkan ke berkas perseorangan (di luar JRA umum)
 *
 * Tanggal dasar:
 *  - pemicu "tahun_anggaran_berakhir" -> 31 Desember tahun acuan
 *  - pemicu lainnya                   -> tanggal acuan (jika diisi) atau tanggal surat
 */
class RetensiService
{
    public const FASE_AKTIF       = 'aktif';
    public const FASE_INAKTIF     = 'inaktif';
    public const FASE_MUSNAH      = 'jatuh_tempo_musnah';
    public const FASE_PERMANEN    = 'serah_arsip_statis';
    public const FASE_DINILAI     = 'perlu_dinilai_kembali';
    public const FASE_PERSEORANGAN = 'berkas_perseorangan';

    public const FASE_LABEL = [
        self::FASE_AKTIF        => 'Aktif',
        self::FASE_INAKTIF      => 'Inaktif',
        self::FASE_MUSNAH       => 'Jatuh Tempo Musnah',
        self::FASE_PERMANEN     => 'Serah ke Arsip Statis',
        self::FASE_DINILAI      => 'Perlu Dinilai Kembali',
        self::FASE_PERSEORANGAN => 'Berkas Perseorangan',
    ];

    /**
     * @return array{status_arsip:string,kriteria_akhir:string,tanggal_inaktif:?string,tanggal_akhir_retensi:?string,fase:string}
     */
    public static function hitung(
        Jra $jra,
        CarbonInterface $tanggalSurat,
        ?CarbonInterface $tanggalAcuan = null,
        ?CarbonInterface $hariIni = null
    ): array {
        $hariIni = ($hariIni ? Carbon::instance($hariIni) : Carbon::today())->startOfDay();

        // Kriteria akhir hanya dua nilai di database. "Dinilai Kembali" & "Berkas Perseorangan"
        // sengaja dipetakan ke Permanen (aman: tidak pernah dimusnahkan otomatis).
        $kriteria = $jra->keterangan === 'Musnah' ? 'Musnah' : 'Permanen';

        if ($jra->keterangan === 'Berkas Perseorangan') {
            return [
                'status_arsip'          => 'Aktif',
                'kriteria_akhir'        => $kriteria,
                'tanggal_inaktif'       => null,
                'tanggal_akhir_retensi' => null,
                'fase'                  => self::FASE_PERSEORANGAN,
            ];
        }

        $dasar = self::tanggalDasar($jra, $tanggalSurat, $tanggalAcuan);
        $mulaiInaktif = $dasar->copy()->addYearsNoOverflow((int) $jra->aktif_tahun);
        $akhirRetensi = $mulaiInaktif->copy()->addYearsNoOverflow((int) $jra->inaktif_tahun);

        if ($hariIni->lt($mulaiInaktif)) {
            $fase = self::FASE_AKTIF;
        } elseif ($hariIni->lt($akhirRetensi)) {
            $fase = self::FASE_INAKTIF;
        } else {
            $fase = match ($jra->keterangan) {
                'Musnah'          => self::FASE_MUSNAH,
                'Permanen'        => self::FASE_PERMANEN,
                default           => self::FASE_DINILAI,
            };
        }

        return [
            'status_arsip'          => $fase === self::FASE_AKTIF ? 'Aktif' : 'Inaktif',
            'kriteria_akhir'        => $kriteria,
            'tanggal_inaktif'       => $mulaiInaktif->toDateString(),
            'tanggal_akhir_retensi' => $akhirRetensi->toDateString(),
            'fase'                  => $fase,
        ];
    }

    public static function tanggalDasar(Jra $jra, CarbonInterface $tanggalSurat, ?CarbonInterface $tanggalAcuan = null): Carbon
    {
        $basis = Carbon::instance($tanggalAcuan ?? $tanggalSurat)->startOfDay();

        if ($jra->pemicu === 'tahun_anggaran_berakhir') {
            return Carbon::create($basis->year, 12, 31)->startOfDay();
        }

        return $basis;
    }
}
