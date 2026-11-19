@extends('layouts.app')

@section('title', 'Dashboard - Sistem Informasi Data Arsip')

@section('content')
<div class="space-y-6">

    {{-- ═══════════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard</h2>
            <p class="text-xs text-slate-500 mt-0.5">Ringkasan data kearsipan surat masuk dan surat keluar</p>
        </div>
        <div>
            <a href="{{ route('arsip.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#1853a8] hover:bg-[#15468d] text-white text-xs font-bold transition-all shadow-md shadow-blue-900/10 active:scale-95">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Arsip</span>
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         5 STAT CARDS (Persis Desain Foto)
    ═══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Total Arsip --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Total Arsip</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-sm border border-sky-100">
                    <i class="fa-regular fa-folder-open"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight font-mono">{{ number_format($totalArsip, 0, ',', '.') }}</span>
                <p class="text-[11px] text-slate-400 mt-1">Surat masuk &amp; keluar</p>
            </div>
        </div>

        {{-- Arsip Aktif --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Arsip Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm border border-emerald-100">
                    <i class="fa-regular fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight font-mono">{{ number_format($arsipAktif, 0, ',', '.') }}</span>
                <p class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Frekuensi tinggi
                </p>
            </div>
        </div>

        {{-- Arsip Inaktif --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Arsip Inaktif</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm border border-amber-100">
                    <i class="fa-regular fa-clock"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight font-mono">{{ number_format($arsipInaktif, 0, ',', '.') }}</span>
                <p class="text-[11px] text-amber-600 font-semibold mt-1 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Retensi menurun
                </p>
            </div>
        </div>

        {{-- Arsip Musnah --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Arsip Musnah</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm border border-rose-100">
                    <i class="fa-regular fa-trash-can"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight font-mono">{{ number_format($arsipMusnah, 0, ',', '.') }}</span>
                <p class="text-[11px] text-rose-600 font-semibold mt-1">Kriteria Nasib Akhir</p>
                @if($arsipJatuhTempoMusnah > 0)
                    <a href="{{ route('arsip.index', ['fase' => 'musnah']) }}" class="inline-block mt-1 text-[11px] font-bold text-rose-700 underline">{{ $arsipJatuhTempoMusnah }} arsip jatuh tempo musnah</a>
                @endif
            </div>
        </div>

        {{-- Arsip Permanen --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Arsip Permanen</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-sm border border-sky-100">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight font-mono">{{ number_format($arsipPermanen, 0, ',', '.') }}</span>
                <p class="text-[11px] text-sky-600 font-semibold mt-1">Kriteria Nasib Akhir</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         TABEL 5 ARSIP TERAKHIR DITAMBAHKAN (Persis Foto)
    ═══════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden">
        {{-- Table Header --}}
        <div class="px-6 py-4 border-b border-slate-200/90 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-1.5 h-4 rounded-full bg-[#1853a8] inline-block"></span>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">5 Arsip Terakhir Ditambahkan</h3>
            </div>
            <a href="{{ route('arsip.index') }}" class="text-xs font-semibold text-sky-600 hover:text-sky-800 flex items-center gap-1.5 transition-colors">
                <span>Lihat Semua Arsip</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        {{-- Table Contents: 8 Kolom Persis Foto --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-12 text-center">NO</th>
                        <th class="py-3.5 px-4">KODE SURAT</th>
                        <th class="py-3.5 px-4">PERIHAL</th>
                        <th class="py-3.5 px-4">TANGGAL</th>
                        <th class="py-3.5 px-4">PENGIRIM</th>
                        <th class="py-3.5 px-4">LETAK FISIK</th>
                        <th class="py-3.5 px-4 text-center">STATUS</th>
                        <th class="py-3.5 px-4 text-center">KRITERIA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($recentArsip as $index => $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- NO --}}
                            <td class="py-3.5 px-4 text-center text-slate-500 font-medium font-mono">
                                {{ $recentArsip->firstItem() + $index }}
                            </td>

                            {{-- KODE SURAT --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <a href="{{ route('arsip.show', $item->id) }}" 
                                   class="px-2.5 py-1 rounded bg-[#ebf3fc] text-[#1b64da] font-mono font-semibold text-xs border border-sky-100 hover:bg-sky-100 transition-colors inline-block"
                                   title="Klik untuk detail arsip">
                                    {{ $item->kode_surat }}
                                </a>
                            </td>

                            {{-- PERIHAL --}}
                            <td class="py-3.5 px-4 font-bold text-slate-800 max-w-xs truncate" title="{{ $item->perihal }}">
                                <a href="{{ route('arsip.show', $item->id) }}" class="hover:text-[#1853a8] transition-colors">
                                    {{ $item->perihal }}
                                </a>
                            </td>

                            {{-- TANGGAL --}}
                            <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap text-xs">
                                {{ $item->tanggal_surat instanceof \Carbon\Carbon ? $item->tanggal_surat->format('d M Y') : date('d M Y', strtotime($item->tanggal_surat)) }}
                            </td>

                            {{-- PENGIRIM --}}
                            <td class="py-3.5 px-4 text-slate-600 max-w-[180px] truncate text-xs" title="{{ $item->pengirim }}">
                                {{ $item->pengirim }}
                            </td>

                            {{-- LETAK FISIK --}}
                            @php
                                $rak = $item->kode_rak ?? '-';
                                $baris = Str::before($item->baris_tingkat, ' ') ?: ($item->baris_tingkat ?? '-');
                                $boks = Str::before($item->nomor_boks, ' (') ?: ($item->nomor_boks ?? '-');
                            @endphp
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-xs text-slate-700 font-medium">
                                    <i class="fa-regular fa-folder-closed text-sky-600 text-[11px]"></i>
                                    <span>{{ $rak }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span>{{ $baris }}</span>
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-[11px] border border-slate-200">
                                        {{ $boks }}
                                    </span>
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($item->status_arsip === 'Aktif')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-100 text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Inaktif
                                    </span>
                                @endif
                            </td>

                            {{-- KRITERIA --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($item->kriteria_akhir === 'Permanen')
                                    <span class="inline-block px-3 py-0.5 rounded-full bg-sky-50 text-sky-600 text-[11px] font-semibold">
                                        Permanen
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-0.5 rounded-full bg-rose-50 text-rose-600 text-[11px] font-semibold">
                                        Musnah
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Belum ada rekaman data arsip.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ═══════════════════════════════════════════
             PAGINATION BAR (Persis Gambar Foto)
        ═══════════════════════════════════════════ --}}
        <div class="px-6 py-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs bg-white">
            {{-- Info Text di Kiri (Real Count dari Database) --}}
            <div class="text-slate-500 text-xs">
                Menampilkan <span class="font-bold text-slate-700">{{ $recentArsip->firstItem() ?? 0 }} - {{ $recentArsip->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-700">{{ number_format($totalArsip, 0, ',', '.') }}</span> arsip
            </div>

            {{-- Tombol Navigasi Halaman di Kanan --}}
            @if($recentArsip->hasPages())
            <div class="flex items-center gap-1 text-xs">
                {{-- Tombol < Sebelumnya --}}
                @if($recentArsip->onFirstPage())
                    <span class="text-slate-300 px-2.5 py-1 select-none cursor-default font-medium">‹ Sebelumnya</span>
                @else
                    <a href="{{ $recentArsip->previousPageUrl() }}" 
                       class="text-slate-600 hover:text-slate-900 px-2.5 py-1 transition-colors font-medium">
                        ‹ Sebelumnya
                    </a>
                @endif

                {{-- Nomor Halaman (Halaman Aktif Kotak Gelap, Lainnya Bersih) --}}
                @php
                    $current = $recentArsip->currentPage();
                    $last = $recentArsip->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $start + 9);
                    $start = max(1, $end - 9);
                @endphp

                @for($p = $start; $p <= $end; $p++)
                    @if($p === $current)
                        <span class="w-7 h-7 bg-[#0e2238] text-white rounded font-bold text-xs flex items-center justify-center shadow-xs select-none">
                            {{ $p }}
                        </span>
                    @else
                        <a href="{{ $recentArsip->url($p) }}" 
                           class="w-7 h-7 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded text-xs flex items-center justify-center transition-colors font-medium">
                            {{ $p }}
                        </a>
                    @endif
                @endfor

                {{-- Tombol Selanjutnya > --}}
                @if($recentArsip->hasMorePages())
                    <a href="{{ $recentArsip->nextPageUrl() }}" 
                       class="text-slate-600 hover:text-slate-900 px-2.5 py-1 transition-colors font-medium">
                        Selanjutnya ›
                    </a>
                @else
                    <span class="text-slate-300 px-2.5 py-1 select-none cursor-default font-medium">Selanjutnya ›</span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
