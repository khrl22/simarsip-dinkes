<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $currentRole = session('simulated_role', auth()->user()->role ?? 'admin');

        $activeQuery = Arsip::where('is_deleted', false);

        // Overall stats
        $totalArsip = (clone $activeQuery)->count();
        $arsipAktif = (clone $activeQuery)->where('status_arsip', 'Aktif')->count();
        $arsipInaktif = (clone $activeQuery)->where('status_arsip', 'Inaktif')->count();
        $klasifikasiMusnah = (clone $activeQuery)->where('kriteria_akhir', 'Musnah')->count();
        $klasifikasiPermanen = (clone $activeQuery)->where('kriteria_akhir', 'Permanen')->count();

        // Rekapitulasi per Tahun Retensi
        $rekapPerTahun = Arsip::where('is_deleted', false)
            ->select(
                'tahun_surat',
                DB::raw('COUNT(*) as total_arsip'),
                DB::raw("SUM(CASE WHEN status_arsip = 'Aktif' THEN 1 ELSE 0 END) as arsip_aktif"),
                DB::raw("SUM(CASE WHEN status_arsip = 'Inaktif' THEN 1 ELSE 0 END) as arsip_inaktif"),
                DB::raw("SUM(CASE WHEN kriteria_akhir = 'Musnah' THEN 1 ELSE 0 END) as kriteria_musnah"),
                DB::raw("SUM(CASE WHEN kriteria_akhir = 'Permanen' THEN 1 ELSE 0 END) as kriteria_permanen")
            )
            ->groupBy('tahun_surat')
            ->orderBy('tahun_surat', 'desc')
            ->get()
            ->map(function ($row) {
                $total = $row->total_arsip ?: 1;
                $row->persentase_aktif = round(($row->arsip_aktif / $total) * 100);
                return $row;
            });

        $persentaseTotal = $totalArsip > 0 ? round(($arsipAktif / $totalArsip) * 100, 1) : 0;

        return view('laporan.index', compact(
            'totalArsip',
            'arsipAktif',
            'arsipInaktif',
            'klasifikasiMusnah',
            'klasifikasiPermanen',
            'rekapPerTahun',
            'persentaseTotal',
            'currentRole'
        ));
    }

    public function exportExcel()
    {
        $rekapPerTahun = Arsip::where('is_deleted', false)
            ->select(
                'tahun_surat',
                DB::raw('COUNT(*) as total_arsip'),
                DB::raw("SUM(CASE WHEN status_arsip = 'Aktif' THEN 1 ELSE 0 END) as arsip_aktif"),
                DB::raw("SUM(CASE WHEN status_arsip = 'Inaktif' THEN 1 ELSE 0 END) as arsip_inaktif"),
                DB::raw("SUM(CASE WHEN kriteria_akhir = 'Musnah' THEN 1 ELSE 0 END) as kriteria_musnah"),
                DB::raw("SUM(CASE WHEN kriteria_akhir = 'Permanen' THEN 1 ELSE 0 END) as kriteria_permanen")
            )
            ->groupBy('tahun_surat')
            ->orderBy('tahun_surat', 'desc')
            ->get();

        $filename = "rekapitulasi_retensi_" . date('Ymd_His') . ".xls";

        $output = "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">";
        $output .= "<head><meta http-equiv=\"content-type\" content=\"application/vnd.ms-excel; charset=UTF-8\"></head>";
        $output .= "<body>";
        $output .= "<h2>LAPORAN KEARSIPAN - REKAPITULASI BERDASARKAN TAHUN RETENSI</h2>";
        $output .= "<table border='1'>";
        $output .= "<thead><tr style='background-color:#0f172a; color:#ffffff;'>";
        $output .= "<th>TAHUN</th><th>TOTAL ARSIP</th><th>ARSIP AKTIF</th><th>ARSIP INAKTIF</th><th>KRITERIA MUSNAH</th><th>KRITERIA PERMANEN</th><th>PERSENTASE AKTIF</th>";
        $output .= "</tr></thead><tbody>";

        $grandTotal = 0;
        $grandAktif = 0;
        $grandInaktif = 0;
        $grandMusnah = 0;
        $grandPermanen = 0;

        foreach ($rekapPerTahun as $r) {
            $grandTotal += $r->total_arsip;
            $grandAktif += $r->arsip_aktif;
            $grandInaktif += $r->arsip_inaktif;
            $grandMusnah += $r->kriteria_musnah;
            $grandPermanen += $r->kriteria_permanen;
            $pct = round(($r->arsip_aktif / ($r->total_arsip ?: 1)) * 100);

            $output .= "<tr>";
            $output .= "<td><b>{$r->tahun_surat}</b></td>";
            $output .= "<td>{$r->total_arsip} berkas</td>";
            $output .= "<td>{$r->arsip_aktif} aktif</td>";
            $output .= "<td>{$r->arsip_inaktif} inaktif</td>";
            $output .= "<td>{$r->kriteria_musnah} musnah</td>";
            $output .= "<td>{$r->kriteria_permanen} permanen</td>";
            $output .= "<td>{$pct}%</td>";
            $output .= "</tr>";
        }

        $grandPct = $grandTotal > 0 ? round(($grandAktif / $grandTotal) * 100, 1) : 0;
        $output .= "<tr style='background-color:#f1f5f9; font-weight:bold;'>";
        $output .= "<td>Total / Keseluruhan</td>";
        $output .= "<td>{$grandTotal} berkas</td>";
        $output .= "<td>{$grandAktif} aktif</td>";
        $output .= "<td>{$grandInaktif} inaktif</td>";
        $output .= "<td>{$grandMusnah} musnah</td>";
        $output .= "<td>{$grandPermanen} permanen</td>";
        $output .= "<td>{$grandPct}%</td>";
        $output .= "</tr>";

        $output .= "</tbody></table></body></html>";

        return response($output, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
}

