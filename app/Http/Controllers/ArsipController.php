<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\AuditLog;
use App\Models\Jra;
use App\Services\RetensiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $currentRole = session('simulated_role', auth()->user()->role ?? 'admin');

        Arsip::sinkronHarian();

        $query = Arsip::with('jra')->where('is_deleted', false);

        // Search query
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%")
                  ->orWhere('kode_klasifikasi', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($tahun = $request->input('tahun')) {
            $query->where('tahun_surat', $tahun);
        }

        if ($status = $request->input('status')) {
            $query->where('status_arsip', $status);
        }

        if ($kriteria = $request->input('kriteria')) {
            $query->where('kriteria_akhir', $kriteria);
        }

        if ($request->input('fase') === 'musnah') {
            $query->jatuhTempoMusnah();
        }

        // Top summary statistics
        $allActive = Arsip::where('is_deleted', false);
        $totalRekaman = (clone $allActive)->count();
        $arsipAktif = (clone $allActive)->where('status_arsip', 'Aktif')->count();
        $arsipInaktif = (clone $allActive)->where('status_arsip', 'Inaktif')->count();
        $arsipPermanen = (clone $allActive)->where('kriteria_akhir', 'Permanen')->count();
        $arsipJatuhTempoMusnah = (clone $allActive)->jatuhTempoMusnah()->count();

        // Get distinct years for filter dropdown
        $availableYears = Arsip::where('is_deleted', false)
            ->distinct()
            ->orderBy('tahun_surat', 'desc')
            ->pluck('tahun_surat');

        // Paginate (6 per page as seen in UI screenshot)
        $arsipList = $query->orderBy('id', 'desc')->paginate(6)->withQueryString();

        return view('arsip.index', compact(
            'arsipList',
            'totalRekaman',
            'arsipAktif',
            'arsipInaktif',
            'arsipPermanen',
            'arsipJatuhTempoMusnah',
            'availableYears',
            'currentRole'
        ));
    }

    public function create()
    {
        $currentYear = date('Y');
        $autoRegNumber = 'REG-' . $currentYear . '-AUTO';
        $currentRole = session('simulated_role', auth()->user()->role ?? 'admin');

        $jraList = Jra::orderBy('kode')->get();

        return view('arsip.create', compact('autoRegNumber', 'currentYear', 'currentRole', 'jraList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_surat' => 'required|string',
            'perihal' => 'required|string',
            'tahun_surat' => 'required|numeric',
            'tanggal_surat' => 'required|date',
            'pengirim' => 'required|string',
            'penerima' => 'required|string',
            'kode_rak' => 'required|string',
            'baris_tingkat' => 'required|string',
            'nomor_boks' => 'required|string',
            'jra_id' => 'required|exists:jra,id',
            'tanggal_acuan_retensi' => 'nullable|date',
            'sifat_naskah' => 'sometimes|nullable|string',
            'tingkat_perkembangan' => 'sometimes|nullable|string',
            'jumlah_lampiran' => 'sometimes|nullable|string',
            'ringkasan_disposisi' => 'nullable|string',
            'depot_penyimpanan' => 'sometimes|nullable|string',
            'kode_klasifikasi' => 'sometimes|nullable|string',
        ]);

        $year = $request->input('tahun_surat');
        $month = date('m', strtotime($request->input('tanggal_surat')));
        $nextSeq = Arsip::count() + 1;

        $uuid = sprintf('ARS-%d-%05d-KU%d', $year, $nextSeq, rand(1, 9));
        $nomorRegistrasi = sprintf('REG-%d-%s-%04d', $year, $month, $nextSeq);
        $nomorAgenda = sprintf('#%04d/AG/%d', $nextSeq, $year);

        $currentUser = auth()->user();
        $creatorName = $currentUser ? "{$currentUser->name} ({$currentUser->jabatan})" : "Petugas (Administrator)";

        $jra = Jra::findOrFail($request->input('jra_id'));
        $retensi = RetensiService::hitung(
            $jra,
            Carbon::parse($request->input('tanggal_surat')),
            $request->filled('tanggal_acuan_retensi') ? Carbon::parse($request->input('tanggal_acuan_retensi')) : null
        );

        $arsip = Arsip::create([
            'uuid' => $uuid,
            'nomor_registrasi' => $nomorRegistrasi,
            'nomor_agenda' => $nomorAgenda,
            'kode_surat' => $request->input('kode_surat'),
            'kode_klasifikasi' => $jra->label,
            'jra_id' => $jra->id,
            'tanggal_acuan_retensi' => $request->input('tanggal_acuan_retensi') ?: null,
            'tanggal_inaktif' => $retensi['tanggal_inaktif'],
            'tanggal_akhir_retensi' => $retensi['tanggal_akhir_retensi'],
            'perihal' => $request->input('perihal'),
            'tahun_surat' => $year,
            'tanggal_surat' => $request->input('tanggal_surat'),
            'pengirim' => $request->input('pengirim'),
            'penerima' => $request->input('penerima'),
            'status_arsip' => $retensi['status_arsip'],
            'kriteria_akhir' => $retensi['kriteria_akhir'],
            'sifat_naskah' => $request->input('sifat_naskah', 'Biasa / Terbuka'),
            'tingkat_perkembangan' => $request->input('tingkat_perkembangan', 'Asli (Tersedia Berkas Fisik & Digital)'),
            'jumlah_lampiran' => $request->input('jumlah_lampiran', '1 Berkas'),
            'ringkasan_disposisi' => $request->input('ringkasan_disposisi'),
            'depot_penyimpanan' => $request->input('depot_penyimpanan', 'Data Arsip Dinkes'),
            'kode_rak' => $request->input('kode_rak'),
            'baris_tingkat' => $request->input('baris_tingkat'),
            'nomor_boks' => $request->input('nomor_boks'),
            'lokasi_status' => 'Tersimpan',
            'created_by' => $creatorName,
        ]);

        AuditLog::create([
            'referensi_audit' => '#' . str_replace('-', '', $nomorRegistrasi),
            'aksi' => 'CREATE',
            'arsip_id' => $arsip->id,
            'kode_surat' => $arsip->kode_surat,
            'perihal' => $arsip->perihal,
            'actor_name' => $currentUser ? $currentUser->name : 'Petugas Sistem',
            'actor_nip' => $currentUser ? $currentUser->nip : '-',
            'actor_role' => $currentUser ? $currentUser->role : 'admin',
            'detail' => 'Penambahan arsip baru ke sistem dengan registrasi ' . $nomorRegistrasi,
        ]);

        return redirect()->route('arsip.index')->with('just_added', [
            'id' => $arsip->id,
            'registrasi' => $arsip->nomor_registrasi,
            'agenda' => $arsip->nomor_agenda,
            'kode_surat' => $arsip->kode_surat,
            'tanggal' => ($arsip->tanggal_surat instanceof \Carbon\Carbon) ? $arsip->tanggal_surat->format('d F Y') : date('d F Y', strtotime((string)$arsip->tanggal_surat)),
            'perihal' => $arsip->perihal,
            'pengirim' => $arsip->pengirim,
            'status' => $arsip->status_arsip,
            'kriteria' => $arsip->kriteria_akhir,
            'lokasi' => "{$arsip->depot_penyimpanan} / {$arsip->kode_rak} • {$arsip->baris_tingkat} • {$arsip->nomor_boks}",
            'creator' => $creatorName,
            'timestamp' => now()->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') . ' WIB',
        ]);
    }

    public function show($id)
    {
        $arsip = Arsip::where('is_deleted', false)->findOrFail($id);
        $currentRole = session('simulated_role', auth()->user()->role ?? 'admin');

        $arsip->load('jra');

        return view('arsip.show', compact('arsip', 'currentRole'));
    }

    public function edit($id)
    {
        $arsip = Arsip::where('is_deleted', false)->findOrFail($id);
        $currentRole = session('simulated_role', auth()->user()->role ?? 'admin');

        $jraList = Jra::orderBy('kode')->get();

        return view('arsip.edit', compact('arsip', 'currentRole', 'jraList'));
    }

    public function update(Request $request, $id)
    {
        $arsip = Arsip::where('is_deleted', false)->findOrFail($id);

        $request->validate([
            'kode_surat' => 'required|string',
            'perihal' => 'required|string',
            'tahun_surat' => 'sometimes|nullable|numeric',
            'tanggal_surat' => 'required|date',
            'pengirim' => 'required|string',
            'penerima' => 'required|string',
            'kode_rak' => 'required|string',
            'baris_tingkat' => 'required|string',
            'nomor_boks' => 'required|string',
            'status_arsip' => 'sometimes|nullable|string',
            'kriteria_akhir' => 'sometimes|nullable|string',
            'sifat_naskah' => 'sometimes|nullable|string',
            'tingkat_perkembangan' => 'sometimes|nullable|string',
            'jumlah_lampiran' => 'sometimes|nullable|string',
            'ringkasan_disposisi' => 'nullable|string',
            'depot_penyimpanan' => 'sometimes|nullable|string',
            'kode_klasifikasi' => 'sometimes|nullable|string',
            'jra_id' => 'nullable|exists:jra,id',
            'tanggal_acuan_retensi' => 'nullable|date',
        ]);

        $tahun = $request->filled('tahun_surat')
            ? (int) $request->input('tahun_surat')
            : date('Y', strtotime($request->input('tanggal_surat')));

        $arsip->update([
            'kode_surat' => $request->input('kode_surat'),
            'kode_klasifikasi' => $request->input('kode_klasifikasi', $arsip->kode_klasifikasi),
            'perihal' => $request->input('perihal'),
            'tanggal_surat' => $request->input('tanggal_surat'),
            'tahun_surat' => $tahun,
            'pengirim' => $request->input('pengirim'),
            'penerima' => $request->input('penerima'),
            'status_arsip' => $request->input('status_arsip', $arsip->status_arsip),
            'kriteria_akhir' => $request->input('kriteria_akhir', $arsip->kriteria_akhir),
            'sifat_naskah' => $request->input('sifat_naskah', $arsip->sifat_naskah),
            'tingkat_perkembangan' => $request->input('tingkat_perkembangan', $arsip->tingkat_perkembangan),
            'jumlah_lampiran' => $request->input('jumlah_lampiran', $arsip->jumlah_lampiran),
            'ringkasan_disposisi' => $request->input('ringkasan_disposisi', $arsip->ringkasan_disposisi),
            'depot_penyimpanan' => $request->input('depot_penyimpanan', $arsip->depot_penyimpanan),
            'kode_rak' => $request->input('kode_rak'),
            'baris_tingkat' => $request->input('baris_tingkat'),
            'nomor_boks' => $request->input('nomor_boks'),
        ]);

        // Bila terhubung ke JRA, status & kriteria dihitung otomatis (input manual diabaikan)
        $arsip->update([
            'jra_id' => $request->input('jra_id') ?: null,
            'tanggal_acuan_retensi' => $request->input('tanggal_acuan_retensi') ?: null,
        ]);
        $arsip->load('jra');
        if ($arsip->jra) {
            $arsip->kode_klasifikasi = $arsip->jra->label;
            $arsip->terapkanRetensi();
        } else {
            $arsip->tanggal_inaktif = null;
            $arsip->tanggal_akhir_retensi = null;
        }
        $arsip->save();

        $arsip->refresh();

        $currentUser = auth()->user();
        $auditRef = '#EDT-' . date('Ymd') . '-' . sprintf('%03d', $arsip->id);
        $creatorName = $currentUser ? "{$currentUser->name} ({$currentUser->jabatan})" : 'Petugas (Administrator)';

        AuditLog::create([
            'referensi_audit' => $auditRef,
            'aksi' => 'UPDATE',
            'arsip_id' => $arsip->id,
            'kode_surat' => $arsip->kode_surat,
            'perihal' => $arsip->perihal,
            'actor_name' => $currentUser ? $currentUser->name : 'Petugas Sistem',
            'actor_nip' => $currentUser ? $currentUser->nip : '-',
            'actor_role' => $currentUser ? $currentUser->role : 'admin',
            'detail' => 'Pembaruan metadata naskah dinas dan lokasi penyimpanan berkas.',
        ]);

        return redirect()->route('arsip.show', $arsip->id)->with('just_updated', [
            'id' => $arsip->id,
            'audit_ref' => $auditRef,
            'registrasi' => $arsip->nomor_registrasi,
            'agenda' => $arsip->nomor_agenda,
            'kode_surat' => $arsip->kode_surat,
            'tanggal' => $arsip->tanggal_surat
                ? (is_string($arsip->tanggal_surat)
                    ? date('d F Y', strtotime($arsip->tanggal_surat))
                    : $arsip->tanggal_surat->translatedFormat('d F Y'))
                : '-',
            'perihal' => $arsip->perihal,
            'pengirim' => $arsip->pengirim,
            'penerima' => $arsip->penerima,
            'status' => $arsip->status_arsip,
            'kriteria' => $arsip->kriteria_akhir,
            'lokasi' => "{$arsip->depot_penyimpanan} / {$arsip->kode_rak} • {$arsip->baris_tingkat} • {$arsip->nomor_boks}",
            'creator' => $creatorName,
            'timestamp' => now()->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') . ' WIB',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $currentRole = session('simulated_role', auth()->user()->role ?? 'admin');
        if ($currentRole !== 'admin') {
            return back()->with('error', 'Hanya Administrator yang memiliki wewenang untuk menghapus arsip.');
        }

        $arsip = Arsip::findOrFail($id);

        $auditRef = '#DEL-' . date('Ymd') . '-' . sprintf('%03d', rand(10, 999));
        $currentUser = auth()->user();
        $actorName = $currentUser ? $currentUser->name : 'Petugas Sistem';
        $actorNip = $currentUser ? $currentUser->nip : '-';

        $kodeSurat = $arsip->kode_surat;
        $perihal = $arsip->perihal;
        $lokasiInfo = "{$arsip->kode_rak} / {$arsip->nomor_boks} (Dihapus permanen dari database)";

        AuditLog::create([
            'referensi_audit' => $auditRef,
            'aksi' => 'DELETE',
            'arsip_id' => $arsip->id,
            'kode_surat' => $kodeSurat,
            'perihal' => $perihal,
            'actor_name' => $actorName,
            'actor_nip' => $actorNip,
            'actor_role' => 'Administrator',
            'detail' => "Penghapusan arsip permanen oleh {$actorName}. Rekaman baris fisik telah dihapus dari database.",
        ]);

        // Hapus permanen dari database
        $arsip->delete();

        return redirect()->route('arsip.index')->with('just_deleted', [
            'kode_surat' => $kodeSurat,
            'perihal' => $perihal,
            'audit_ref' => $auditRef,
            'waktu' => now()->translatedFormat('d M Y, H:i') . ' WIB',
            'otorisator' => "{$actorName} (NIP: {$actorNip})",
            'lokasi_info' => $lokasiInfo,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $query = Arsip::where('is_deleted', false);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('tahun_surat', 'desc')->get();

        $filename = "rekapitulasi_arsip_" . date('Ymd_His') . ".xls";

        $output = "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">";
        $output .= "<head><meta http-equiv=\"content-type\" content=\"application/vnd.ms-excel; charset=UTF-8\"></head>";
        $output .= "<body><table border='1'>";
        $output .= "<thead><tr style='background-color:#0f172a; color:#ffffff;'><th>No</th><th>Kode Surat</th><th>Nomor Registrasi</th><th>Perihal</th><th>Tanggal Surat</th><th>Pengirim</th><th>Penerima</th><th>Tahun</th><th>Status</th><th>Kriteria</th><th>Lokasi Rak</th><th>Boks</th></tr></thead><tbody>";
        $no = 1;
        foreach ($items as $item) {
            $output .= "<tr>";
            $output .= "<td>" . $no++ . "</td>";
            $output .= "<td>" . htmlspecialchars($item->kode_surat) . "</td>";
            $output .= "<td>" . htmlspecialchars($item->nomor_registrasi) . "</td>";
            $output .= "<td>" . htmlspecialchars($item->perihal) . "</td>";
            $output .= "<td>" . $item->tanggal_surat->format('d/m/Y') . "</td>";
            $output .= "<td>" . htmlspecialchars($item->pengirim) . "</td>";
            $output .= "<td>" . htmlspecialchars($item->penerima) . "</td>";
            $output .= "<td>" . $item->tahun_surat . "</td>";
            $output .= "<td>" . $item->status_arsip . "</td>";
            $output .= "<td>" . $item->kriteria_akhir . "</td>";
            $output .= "<td>" . htmlspecialchars($item->kode_rak) . "</td>";
            $output .= "<td>" . htmlspecialchars($item->nomor_boks) . "</td>";
            $output .= "</tr>";
        }
        $output .= "</tbody></table></body></html>";

        return response($output, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $items = Arsip::where('is_deleted', false)->orderBy('id', 'desc')->get();
        return view('arsip.print-pdf', compact('items'));
    }

    public function printBarcode($id)
    {
        $arsip = Arsip::where('is_deleted', false)->findOrFail($id);
        return view('arsip.print-barcode', compact('arsip'));
    }
}
