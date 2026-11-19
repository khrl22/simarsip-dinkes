@extends('layouts.app')

@section('title', 'Laporan Kearsipan - Sistem Informasi Data Arsip')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[11px] font-bold text-sky-700 uppercase tracking-widest flex items-center gap-1.5 mb-1">
                <i class="fa-regular fa-file-lines"></i>
                <span>DOKUMENTASI & REKAPITULASI</span>
            </span>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Laporan Kearsipan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi data arsip surat masuk dan keluar</p>
        </div>
        <div>
            <a href="{{ route('laporan.export.excel') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#0d2847] hover:bg-[#12365e] text-white text-xs font-bold transition-all shadow-md shadow-slate-900/10 active:scale-95">
                <i class="fa-regular fa-file-excel text-sm text-emerald-400"></i>
                <span>Ekspor ke Excel (.xlsx)</span>
            </a>
        </div>
    </div>

    <!-- 5 STAT CARDS (Image 1 batch 1) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Arsip Terdata -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-600">Total Arsip Terdata</span>
                <i class="fa-regular fa-folder text-slate-400 text-sm"></i>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ number_format($totalArsip, 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-500 font-medium">Berkas</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Semua entitas tercatat</p>
            </div>
        </div>

        <!-- Arsip Aktif -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-600">Arsip Aktif</span>
                <div class="w-6 h-6 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ number_format($arsipAktif, 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-500 font-medium">Berkas</span>
                </div>
                <p class="text-[11px] text-sky-700 font-semibold mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> OPERASIONAL HARIAN
                </p>
            </div>
        </div>

        <!-- Arsip Inaktif -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-600">Arsip Inaktif</span>
                <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ number_format($arsipInaktif, 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-500 font-medium">Berkas</span>
                </div>
                <p class="text-[11px] text-slate-600 font-semibold mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-slate-600"></span> PUSAT PENYIMPANAN
                </p>
            </div>
        </div>

        <!-- Klasifikasi Musnah -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-600">Klasifikasi Musnah</span>
                <div class="w-6 h-6 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                    <i class="fa-regular fa-trash-can"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ number_format($klasifikasiMusnah, 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-500 font-medium">Berkas</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Kriteria Akhir Retensi</p>
            </div>
        </div>

        <!-- Klasifikasi Permanen -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-600">Klasifikasi Permanen</span>
                <div class="w-6 h-6 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-ribbon"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ number_format($klasifikasiPermanen, 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-500 font-medium">Berkas</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Nilai Guna Abadi</p>
            </div>
        </div>
    </div>

    <!-- REKAPITULASI BERDASARKAN TAHUN RETENSI TABLE (Image 1 batch 1) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <i class="fa-regular fa-calendar-days text-sky-600 text-sm"></i>
                <h3 class="text-xs font-bold text-slate-900 tracking-tight">Rekapitulasi Berdasarkan Tahun Retensi</h3>
            </div>
            <span class="text-[11px] text-slate-400 font-medium">Unit Pengolah: Seluruh Satuan Kerja</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">TAHUN</th>
                        <th class="py-3.5 px-6">TOTAL ARSIP</th>
                        <th class="py-3.5 px-6">ARSIP AKTIF</th>
                        <th class="py-3.5 px-6">ARSIP INAKTIF</th>
                        <th class="py-3.5 px-6">KRITERIA MUSNAH</th>
                        <th class="py-3.5 px-6">KRITERIA PERMANEN</th>
                        <th class="py-3.5 px-6 text-center">PERSENTASE AKTIF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium">
                    @forelse($rekapPerTahun as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-bold text-slate-900 font-mono">
                                {{ $row->tahun_surat }}
                            </td>
                            <td class="py-4 px-6 text-slate-700">
                                <span class="font-bold">{{ number_format($row->total_arsip, 0, ',', '.') }}</span> berkas
                            </td>
                            <td class="py-4 px-6 text-sky-700 font-semibold">
                                {{ number_format($row->arsip_aktif, 0, ',', '.') }} aktif
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ number_format($row->arsip_inaktif, 0, ',', '.') }} inaktif
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ number_format($row->kriteria_musnah, 0, ',', '.') }} musnah
                            </td>
                            <td class="py-4 px-6 text-slate-800">
                                {{ number_format($row->kriteria_permanen, 0, ',', '.') }} permanen
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-block px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-700 text-xs font-bold border border-sky-100 font-mono">
                                    {{ $row->persentase_aktif }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data rekapitulasi retensi.</td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Summary Total Row (Image 1 batch 1) -->
                <tfoot>
                    <tr class="bg-sky-50/60 border-t-2 border-sky-200/80 text-xs font-bold text-slate-900">
                        <td class="py-4 px-6 font-bold">
                            Total / Keseluruhan
                        </td>
                        <td class="py-4 px-6 font-mono">
                            {{ number_format($totalArsip, 0, ',', '.') }} berkas
                        </td>
                        <td class="py-4 px-6 font-mono text-sky-800">
                            {{ number_format($arsipAktif, 0, ',', '.') }} aktif
                        </td>
                        <td class="py-4 px-6 font-mono text-slate-700">
                            {{ number_format($arsipInaktif, 0, ',', '.') }} inaktif
                        </td>
                        <td class="py-4 px-6 font-mono text-slate-700">
                            {{ number_format($klasifikasiMusnah, 0, ',', '.') }} musnah
                        </td>
                        <td class="py-4 px-6 font-mono text-slate-900">
                            {{ number_format($klasifikasiPermanen, 0, ',', '.') }} permanen
                        </td>
                        <td class="py-4 px-6 text-center font-mono text-sm text-sky-900">
                            {{ $persentaseTotal }}%
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- PETUNJUK TEKNIS STATUS ARSIP (Image 1 batch 1) -->
    <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex items-start gap-3 text-xs text-slate-600">
        <i class="fa-solid fa-circle-info text-sky-600 text-sm mt-0.5 flex-shrink-0"></i>
        <div class="space-y-1">
            <strong class="text-slate-800 font-bold block">Petunjuk Teknis Status Arsip</strong>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Catatan: Klasifikasi Musnah dan Permanen merupakan penentuan kriteria jadwal retensi naskah dinas, bukan tanda bahwa proses pemusnahan fisik telah dilakukan.
            </p>
        </div>
    </div>
</div>
@endsection

