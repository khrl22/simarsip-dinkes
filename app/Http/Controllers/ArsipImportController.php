<?php

namespace App\Http\Controllers;

use App\Services\ArsipImportService;
use App\Services\SpreadsheetReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArsipImportController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:5120|extensions:xlsx,xls,csv',
            'kode_rak' => 'nullable|string|max:100',
            'baris_tingkat' => 'nullable|string|max:100',
            'nomor_boks' => 'nullable|string|max:100',
        ], [
            'file.required' => 'Pilih file Excel/CSV terlebih dahulu.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
            'file.extensions' => 'Format file harus .xlsx, .xls, atau .csv.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('arsip.index')->with('error', $validator->errors()->first());
        }

        @set_time_limit(300);

        try {
            $rows = SpreadsheetReader::read($request->file('file')->getRealPath());
            $result = ArsipImportService::import($rows, [
                'kode_rak' => $request->input('kode_rak'),
                'baris_tingkat' => $request->input('baris_tingkat'),
                'nomor_boks' => $request->input('nomor_boks'),
            ], auth()->user());
        } catch (\RuntimeException $e) {
            return redirect()->route('arsip.index')->with('error', $e->getMessage());
        }

        return redirect()->route('arsip.index')->with('import_result', $result);
    }

    /** Template berupa tabel HTML (.xls) yang bisa dibuka Excel dan dibaca kembali oleh impor. */
    public function template()
    {
        $head = ['Nomor Surat', 'Tanggal Surat', 'Pengirim', 'Penerima', 'Perihal', 'Kode JRA', 'Kode Rak', 'Baris/Tingkat', 'Nomor Boks'];
        $contoh = [
            ['CONTOH 800/123/Pusk-UB/2023', '15/03/2023', 'UPTD Puskesmas Ujung Berung Indah', 'Kepala Dinas Kesehatan Kota Bandung', 'Usul kenaikan pangkat (hapus baris contoh ini)', 'B.001', 'Rak A-01', 'Baris 01 / Tingkat 2', 'Boks 05'],
        ];

        $out = '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"></head><body><table border="1"><thead><tr>';
        foreach ($head as $h) {
            $out .= '<th style="background:#0f172a;color:#fff">' . htmlspecialchars($h) . '</th>';
        }
        $out .= '</tr></thead><tbody>';
        foreach ($contoh as $r) {
            $out .= '<tr>' . implode('', array_map(fn ($c) => '<td style="mso-number-format:\'\\@\'">' . htmlspecialchars($c) . '</td>', $r)) . '</tr>';
        }
        $out .= '</tbody></table></body></html>';

        return response($out, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="template_impor_arsip.xls"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
