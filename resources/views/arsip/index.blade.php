@extends('layouts.app')

@section('title', 'Data Arsip - Sistem Informasi Data Arsip')

@section('content')
<div class="space-y-6">

    {{-- ═══════════════════════════════════════════
         PAGE HEADER (Persis Foto)
    ═══════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Data Arsip</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar dan pencarian rekaman arsip surat lengkap dengan riwayat stempel waktu input sistem</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="document.getElementById('modalImpor').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold transition-all active:scale-95">
                <i class="fa-regular fa-file-excel text-xs"></i>
                <span>Impor Excel</span>
            </button>
            <a href="{{ route('arsip.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#1853a8] hover:bg-[#15468d] text-white text-xs font-bold transition-all shadow-md shadow-blue-900/10 active:scale-95">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Arsip</span>
            </a>
        </div>
    </div>


    {{-- Hasil impor --}}
    @if(session('import_result'))
        @php $ir = session('import_result'); @endphp
        <div class="rounded-xl border {{ $ir['gagal'] > 0 ? 'border-amber-200 bg-amber-50' : 'border-emerald-200 bg-emerald-50' }} p-4 text-xs text-slate-700">
            <p class="font-bold text-slate-900 mb-1">Impor selesai: {{ $ir['berhasil'] }} arsip berhasil ditambahkan dari {{ $ir['total'] }} baris.</p>
            <ul class="list-disc ml-5 space-y-0.5">
                @if($ir['duplikat'] > 0)<li>{{ $ir['duplikat'] }} baris dilewati karena sudah ada (kode surat, tanggal, dan pengirim sama).</li>@endif
                @if($ir['gagal'] > 0)<li>{{ $ir['gagal'] }} baris gagal diimpor.</li>@endif
                @if($ir['tanpa_jra'] > 0)<li>{{ $ir['tanpa_jra'] }} arsip belum terhubung ke JRA (status Aktif / Permanen). Pilih JRA lewat menu Edit agar status otomatis.</li>@endif
            </ul>
            @if(!empty($ir['errors']))
                <details class="mt-2"><summary class="cursor-pointer font-bold">Lihat rincian baris bermasalah</summary>
                    <ul class="mt-1 space-y-0.5">
                        @foreach(array_slice($ir['errors'], 0, 20) as $err)<li>{{ $err }}</li>@endforeach
                        @if(count($ir['errors']) > 20)<li>... dan {{ count($ir['errors']) - 20 }} lainnya.</li>@endif
                    </ul>
                </details>
            @endif
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         4 TOP STAT CARDS (Persis Desain Foto)
    ═══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Rekaman --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl border border-sky-100 flex-shrink-0">
                <i class="fa-regular fa-folder-open"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL REKAMAN</span>
                <span class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ number_format($totalRekaman, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Arsip Aktif --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl border border-sky-100 flex-shrink-0">
                <i class="fa-regular fa-calendar-check"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">ARSIP AKTIF</span>
                <span class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ number_format($arsipAktif, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Arsip Inaktif --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl border border-sky-100 flex-shrink-0">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">ARSIP INAKTIF</span>
                <span class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ number_format($arsipInaktif, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Arsip Permanen --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl border border-sky-100 flex-shrink-0">
                <i class="fa-regular fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">ARSIP PERMANEN</span>
                <span class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ number_format($arsipPermanen, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         SEARCH & FILTER TOOLBAR (Persis Foto)
    ═══════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-xs space-y-3">
        <form action="{{ route('arsip.index') }}" method="GET" class="space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                {{-- Search Input --}}
                <div class="flex-1 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari kode surat, perihal, atau instansi..." 
                           class="w-full pl-9 pr-3.5 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 transition-all placeholder:text-slate-400">
                </div>

                {{-- Dropdown Filters --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Tahun --}}
                    <select name="tahun" onchange="this.form.submit()" class="py-2 px-3 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white text-slate-700 font-medium">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                        @endforeach
                    </select>

                    {{-- Status --}}
                    <select name="status" onchange="this.form.submit()" class="py-2 px-3 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white text-slate-700 font-medium">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Inaktif" {{ request('status') == 'Inaktif' ? 'selected' : '' }}>Inaktif</option>
                    </select>

                    {{-- Kriteria --}}
                    <select name="kriteria" onchange="this.form.submit()" class="py-2 px-3 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white text-slate-700 font-medium">
                        <option value="">Semua Kriteria</option>
                        <option value="Permanen" {{ request('kriteria') == 'Permanen' ? 'selected' : '' }}>Permanen</option>
                        <option value="Musnah" {{ request('kriteria') == 'Musnah' ? 'selected' : '' }}>Musnah</option>
                    </select>

                    {{-- Jatuh tempo musnah --}}
                    <select name="fase" onchange="this.form.submit()" class="py-2 px-3 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white text-slate-700 font-medium">
                        <option value="">Semua Fase</option>
                        <option value="musnah" {{ request('fase') == 'musnah' ? 'selected' : '' }}>Jatuh Tempo Musnah ({{ $arsipJatuhTempoMusnah }})</option>
                    </select>

                    {{-- Reset Button --}}
                    @if(request()->hasAny(['search', 'tahun', 'status', 'kriteria', 'fase']))
                        <a href="{{ route('arsip.index') }}" class="py-2 px-3 text-xs rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-medium flex items-center gap-1.5 transition-colors">
                            <i class="fa-solid fa-rotate-left text-[10px]"></i>
                            <span>Reset</span>
                        </a>
                    @else
                        <button type="reset" onclick="window.location='{{ route('arsip.index') }}'" class="py-2 px-3 text-xs rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-medium flex items-center gap-1.5 transition-colors">
                            <i class="fa-solid fa-rotate-left text-[10px]"></i>
                            <span>Reset</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Bottom Row: Filter Info, Badge Pencatatan Otomatis (WIB), & Export Buttons --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-100 text-xs">
                <div class="flex flex-wrap items-center gap-2.5 text-slate-500">
                    <div class="flex items-center gap-1.5">
                        <i class="fa-solid fa-sliders text-slate-400"></i>
                        <span>Filter Aktif:</span>
                        <span class="font-bold text-slate-700">
                            @if(request('tahun') || request('status') || request('kriteria') || request('search'))
                                {{ request('status') ? 'Status ' . request('status') : '' }} 
                                {{ request('kriteria') ? 'Kriteria ' . request('kriteria') : '' }} 
                                {{ request('tahun') ? 'Tahun ' . request('tahun') : '' }}
                                {{ request('search') ? 'Pencarian: "' . request('search') . '"' : '' }}
                            @else
                                Semua Kategori
                            @endif
                        </span>
                    </div>

                    {{-- Badge Pencatatan Otomatis (WIB) Persis Foto --}}
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-200/80 text-[11px] font-semibold">
                        <i class="fa-regular fa-clock text-[10px]"></i>
                        <span>Pencatatan Otomatis (WIB)</span>
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('arsip.export.excel', request()->all()) }}" 
                       class="px-3.5 py-1.5 rounded-lg border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-slate-700 font-medium text-xs flex items-center gap-2 transition-all">
                        <i class="fa-regular fa-file-excel text-emerald-600"></i>
                        <span>Ekspor Excel</span>
                    </a>
                    <a href="{{ route('arsip.export.pdf') }}" 
                       target="_blank"
                       class="px-3.5 py-1.5 rounded-lg border border-slate-200 hover:border-rose-300 hover:bg-rose-50 text-slate-700 font-medium text-xs flex items-center gap-2 transition-all">
                        <i class="fa-regular fa-file-pdf text-rose-600"></i>
                        <span>Ekspor PDF</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════
         MAIN DATA TABLE (10 Kolom Persis Foto Baru)
    ═══════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-3 w-10 text-center">No</th>
                        <th class="py-3.5 px-3">Kode Surat</th>
                        <th class="py-3.5 px-3 min-w-[200px]">Perihal</th>
                        <th class="py-3.5 px-3 whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <span>Tanggal &amp; Waktu Input</span>
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-sky-100/70 text-sky-800 text-[9px] font-semibold border border-sky-200 lowercase">
                                    <i class="fa-regular fa-clock text-[8px]"></i>
                                    <span>Terbaru ↓</span>
                                </span>
                            </div>
                        </th>
                        <th class="py-3.5 px-3">Pengirim</th>
                        <th class="py-3.5 px-3">Penerima</th>
                        <th class="py-3.5 px-2 text-center">Tahun</th>
                        <th class="py-3.5 px-2 text-center">Status</th>
                        <th class="py-3.5 px-2 text-center">Kriteria</th>
                        <th class="py-3.5 px-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($arsipList as $index => $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- 1. No --}}
                            <td class="py-3 px-3 text-center text-slate-400 font-mono">
                                {{ $arsipList->firstItem() + $index }}
                            </td>

                            {{-- 2. Kode Surat --}}
                            <td class="py-3 px-3 whitespace-nowrap">
                                <a href="{{ route('arsip.show', $item->id) }}" 
                                   class="px-2.5 py-1 rounded bg-[#ebf3fc] text-[#1b64da] font-mono font-semibold text-xs border border-sky-100 hover:bg-sky-100 transition-colors inline-block"
                                   title="Klik untuk detail & kelola arsip">
                                    {{ $item->kode_surat }}
                                </a>
                            </td>

                            {{-- 3. Perihal & Lampiran --}}
                            <td class="py-3 px-3">
                                <a href="{{ route('arsip.show', $item->id) }}" class="font-bold text-slate-900 hover:text-[#1853a8] transition-colors block leading-snug">
                                    {{ $item->perihal }}
                                </a>
                                @php
                                    $lampiranText = $item->jumlah_lampiran;
                                    if ($lampiranText && !Str::startsWith($lampiranText, 'Lampiran:')) {
                                        $lampiranText = 'Lampiran: ' . $lampiranText;
                                    }
                                @endphp
                                <span class="text-[11px] text-slate-400 mt-0.5 block font-medium">
                                    {{ $lampiranText ?: 'Lampiran: 2 Berkas' }}
                                </span>
                            </td>

                            {{-- 4. Tanggal & Waktu Input (dengan Stempel WIB persis foto) --}}
                            @php
                                $tglFormatted = $item->tanggal_surat instanceof \Carbon\Carbon ? $item->tanggal_surat->format('d M Y') : date('d M Y', strtotime($item->tanggal_surat));
                                $jamInput = $item->created_at ? $item->created_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB' : now()->timezone('Asia/Jakarta')->format('H:i') . ' WIB';
                            @endphp
                            <td class="py-3 px-3 whitespace-nowrap">
                                <div class="text-xs text-slate-700 font-medium">{{ $tglFormatted }}</div>
                                <div class="mt-0.5">
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-sky-50 text-sky-700 font-mono text-[10px] font-semibold border border-sky-100">
                                        <i class="fa-regular fa-clock text-[9px]"></i>
                                        <span>{{ $jamInput }}</span>
                                    </span>
                                </div>
                            </td>

                            {{-- 5. Pengirim --}}
                            <td class="py-3 px-3 text-slate-700 font-medium">
                                {{ $item->pengirim }}
                            </td>

                            {{-- 6. Penerima --}}
                            <td class="py-3 px-3 text-slate-700">
                                {{ $item->penerima }}
                            </td>

                            {{-- 7. Tahun --}}
                            <td class="py-3 px-2 text-center font-bold text-slate-800 font-mono">
                                {{ $item->tahun_surat }}
                            </td>

                            {{-- 8. Status --}}
                            <td class="py-3 px-2 text-center whitespace-nowrap">
                                @if($item->status_arsip === 'Aktif')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-100 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inaktif
                                    </span>
                                @endif
                                @if($item->fase_retensi === 'jatuh_tempo_musnah')
                                    <span class="block mt-1 text-[9px] font-bold text-rose-600">Siap dimusnahkan</span>
                                @elseif($item->fase_retensi === 'perlu_dinilai_kembali')
                                    <span class="block mt-1 text-[9px] font-bold text-violet-600">Perlu dinilai kembali</span>
                                @elseif($item->fase_retensi === 'serah_arsip_statis')
                                    <span class="block mt-1 text-[9px] font-bold text-sky-600">Serah ke arsip statis</span>
                                @endif
                            </td>

                            {{-- 9. Kriteria --}}
                            <td class="py-3 px-2 text-center whitespace-nowrap">
                                @if($item->kriteria_akhir === 'Permanen')
                                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-600 text-[10px] font-semibold">
                                        Permanen
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-600 text-[10px] font-semibold">
                                        Musnah
                                    </span>
                                @endif
                            </td>

                            {{-- 10. Aksi (Mata, Edit, Hapus) --}}
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- Detail --}}
                                    <a href="{{ route('arsip.show', $item->id) }}" 
                                       title="Lihat Detail Arsip"
                                       class="p-1 rounded hover:bg-sky-50 text-sky-600 transition-colors">
                                        <i class="fa-regular fa-eye text-xs"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('arsip.edit', $item->id) }}" 
                                       title="Edit Berkas Arsip"
                                       class="p-1 rounded hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-colors">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>

                                    {{-- Hapus --}}
                                    @if($currentRole === 'admin')
                                        <button type="button" 
                                                onclick="openDeleteModal({{ json_encode($item) }})"
                                                title="Hapus Arsip"
                                                class="p-1 rounded hover:bg-rose-50 text-rose-500 hover:text-rose-700 transition-colors cursor-pointer">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    @else
                                        <button type="button" 
                                                disabled
                                                title="Mode Staf tidak memiliki izin hapus"
                                                class="p-1 rounded text-slate-300 cursor-not-allowed">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                    <i class="fa-regular fa-folder-open"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-700">Tidak ada arsip yang cocok</p>
                                <p class="text-xs text-slate-400 mt-0.5">Coba gunakan kata kunci pencarian atau filter lain.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ═══════════════════════════════════════════
             CUSTOM PAGINATION (Persis Foto)
        ═══════════════════════════════════════════ --}}
        <div class="px-6 py-4 border-t border-slate-200 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-600">
            <div>
                Menampilkan <strong class="font-bold text-slate-800">{{ $arsipList->firstItem() ?? 0 }}–{{ $arsipList->lastItem() ?? 0 }}</strong> dari <strong class="font-bold text-slate-800">{{ number_format($arsipList->total(), 0, ',', '.') }}</strong> data arsip
            </div>
            
            @if($arsipList->hasPages())
            <div class="flex items-center gap-1 font-medium">
                {{-- Tombol Sebelumnya --}}
                @if($arsipList->onFirstPage())
                    <span class="px-2.5 py-1 text-slate-300 cursor-not-allowed select-none">‹ Sebelumnya</span>
                @else
                    <a href="{{ $arsipList->previousPageUrl() }}" class="px-2.5 py-1 text-slate-600 hover:text-slate-900 transition-colors">‹ Sebelumnya</a>
                @endif

                {{-- Nomor Halaman --}}
                @php
                    $currentPage = $arsipList->currentPage();
                    $lastPage = $arsipList->lastPage();
                @endphp

                @for($p = 1; $p <= min(3, $lastPage); $p++)
                    @if($p == $currentPage)
                        <span class="w-7 h-7 rounded bg-[#13437a] text-white font-bold flex items-center justify-center shadow-xs select-none">{{ $p }}</span>
                    @else
                        <a href="{{ $arsipList->url($p) }}" class="w-7 h-7 rounded hover:bg-slate-100 text-slate-600 flex items-center justify-center transition-colors">{{ $p }}</a>
                    @endif
                @endfor

                @if($lastPage > 4)
                    <span class="px-1 text-slate-400">...</span>
                    @if($currentPage > 3 && $currentPage < $lastPage)
                        <span class="w-7 h-7 rounded bg-[#13437a] text-white font-bold flex items-center justify-center shadow-xs select-none">{{ $currentPage }}</span>
                        <span class="px-1 text-slate-400">...</span>
                    @endif
                    <a href="{{ $arsipList->url($lastPage) }}" class="w-7 h-7 rounded hover:bg-slate-100 text-slate-600 flex items-center justify-center transition-colors {{ $currentPage == $lastPage ? 'bg-[#13437a] text-white font-bold' : '' }}">{{ $lastPage }}</a>
                @endif

                {{-- Tombol Berikutnya --}}
                @if($arsipList->hasMorePages())
                    <a href="{{ $arsipList->nextPageUrl() }}" class="px-2.5 py-1 text-slate-600 hover:text-slate-900 transition-colors">Berikutnya ›</a>
                @else
                    <span class="px-2.5 py-1 text-slate-300 cursor-not-allowed select-none">Berikutnya ›</span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus Data Arsip --}}
@include('arsip.partials.modal-delete')


{{-- ═══ MODAL IMPOR EXCEL ═══ --}}
<div id="modalImpor" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
    <form action="{{ route('arsip.import.store') }}" method="POST" enctype="multipart/form-data"
          onsubmit="this.querySelector('[data-submit]').disabled = true; this.querySelector('[data-submit]').textContent = 'Mengimpor...';"
          class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[92vh] overflow-y-auto p-6 space-y-4">
        @csrf
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center"><i class="fa-regular fa-file-excel"></i></div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Impor Data Arsip dari Excel</h3>
                    <p class="text-[11px] text-slate-500">Format .xlsx, .xls, atau .csv (maks. 5 MB)</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modalImpor').classList.add('hidden')" class="text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="rounded-lg bg-sky-50 border border-sky-100 p-3 text-[11px] text-slate-600 leading-relaxed">
            Bisa memakai template di bawah, atau file berformat "Daftar Isi Berkas" (kolom URAIAN INFORMASI ARSIP): nomor surat, pengirim, penerima, dan perihal akan diambil dari kalimat uraian. Baris yang sudah ada (kode surat, tanggal, dan pengirim sama) akan dilewati. Isi kolom "Kode JRA" (mis. B.001) agar status Aktif/Inaktif/Musnah dihitung otomatis.
        </div>

        <a href="{{ route('arsip.import.template') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 hover:text-sky-700">
            <i class="fa-solid fa-download"></i> Unduh template Excel
        </a>

        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">File Excel</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                   class="block w-full text-xs text-slate-600 file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 file:font-bold">
        </div>

        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Lokasi simpan bawaan (opsional)</label>
            <p class="text-[11px] text-slate-400 mb-2">Dipakai untuk baris yang tidak punya data rak/boks. Jika dikosongkan, diisi tanda "-" dan bisa diedit nanti.</p>
            <div class="grid grid-cols-3 gap-2">
                <input type="text" name="kode_rak" placeholder="Kode Rak" class="px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
                <input type="text" name="baris_tingkat" placeholder="Baris / Tingkat" class="px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
                <input type="text" name="nomor_boks" placeholder="Nomor Boks" class="px-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <button type="button" onclick="document.getElementById('modalImpor').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900">Batal</button>
            <button type="submit" data-submit class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#1853a8] hover:bg-[#15468d] text-white text-xs font-bold disabled:opacity-60">
                <i class="fa-solid fa-upload text-xs"></i> Impor Sekarang
            </button>
        </div>
    </form>
</div>

@endsection
