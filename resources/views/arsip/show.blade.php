@extends('layouts.app')

@section('title', 'Detail Arsip — ' . $arsip->kode_surat)

@section('content')
@php
    // Helper format untuk klasifikasi, nomor registrasi, koordinat, dan tanggal sesuai database & desain
    $rawKlasifikasi = $arsip->kode_klasifikasi ?? '';
    if (str_contains($rawKlasifikasi, '-')) {
        $kodeKlasifikasiShort = trim(Str::before($rawKlasifikasi, '-'));
        $namaKlasifikasi = trim(Str::after($rawKlasifikasi, '-'));
    } else {
        $kodeKlasifikasiShort = trim($rawKlasifikasi) ?: 'KU.01.02';
        $namaKlasifikasi = 'Keuangan & Anggaran (RKA/DPA)';
    }

    $rawReg = $arsip->nomor_registrasi ?? '';
    $idArsipDisplay = $rawReg ? ('#' . ltrim(Str::replace('REG-', 'RS-', $rawReg), '#')) : '#RS-' . date('Y') . '-0084';

    $tglRegistrasi = $arsip->created_at 
        ? $arsip->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y') 
        : ($arsip->tanggal_surat ? $arsip->tanggal_surat->timezone('Asia/Jakarta')->translatedFormat('d M Y') : date('d M Y'));

    $nomorRakOnly = trim(Str::replace(['Rak', 'rak', 'RAK'], '', $arsip->kode_rak ?? 'A-02')) ?: 'A-02';
    $barisTkOnly = trim(Str::before($arsip->baris_tingkat ?? 'B03', ' ')) ?: 'B03';
    $boksRaw = Str::before($arsip->nomor_boks ?? '#14', ' (');
    $nomorBoksOnly = trim(Str::replace(['Boks', 'boks', 'BOKS'], '', $boksRaw)) ?: '#14';
    if (!Str::startsWith($nomorBoksOnly, '#')) {
        $nomorBoksOnly = '#' . $nomorBoksOnly;
    }
@endphp

<div class="space-y-4">

    {{-- ═══════════════════════════════════════════════════════
         ROW 1: BREADCRUMB & IDENTIFIER BADGES (Persis Foto)
    ═══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
        {{-- Breadcrumb Kiri --}}
        <div class="flex items-center gap-2 font-medium text-slate-500">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-800 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-table-cells-large text-[11px]"></i>
                <span>Dashboard</span>
            </a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('arsip.index') }}" class="hover:text-slate-800 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-box-archive text-[11px]"></i>
                <span>Data Arsip</span>
            </a>
            <span class="text-slate-300">/</span>
            <span class="font-bold text-slate-800 font-mono flex items-center gap-1.5">
                <i class="fa-regular fa-folder-open text-sky-600 text-xs"></i>
                <span>{{ $arsip->kode_surat }}</span>
            </span>
        </div>

        {{-- Badge Kanan: ID Arsip & Tanggal Registrasi --}}
        <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-white border border-slate-200 text-slate-600 font-mono text-[11px] font-semibold shadow-2xs">
                <i class="fa-solid fa-hashtag text-sky-500 text-[10px]"></i>
                <span>ID ARSIP : <strong class="text-slate-900">{{ $idArsipDisplay }}</strong></span>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-white border border-slate-200 text-slate-600 text-[11px] font-medium shadow-2xs">
                <i class="fa-regular fa-calendar-check text-slate-400 text-xs"></i>
                <span>Registrasi: <strong class="text-slate-800 font-semibold">{{ $tglRegistrasi }}</strong></span>
            </span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         ROW 2: ACTION & STATUS TOOLBAR (Persis Foto)
    ═══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs px-5 py-3.5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        
        {{-- Kiri: Kembali + Badges Status --}}
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Tombol Kembali --}}
            <a href="{{ route('arsip.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Kembali ke Data Arsip</span>
            </a>
            <span class="text-slate-300">•</span>

            {{-- Badge Klasifikasi --}}
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-mono text-[11px] font-bold border border-slate-200">
                Klasifikasi: {{ $kodeKlasifikasiShort }}
            </span>

            {{-- Badge Status Aktif/Inaktif --}}
            @if($arsip->status_arsip === 'Aktif')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>AKTIF (UNIT PENGOLAH)</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>INAKTIF (PUSAT PENYIMPANAN)</span>
                </span>
            @endif

            {{-- Badge Kriteria Permanen/Musnah --}}
            @if($arsip->kriteria_akhir === 'Permanen')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-700 text-[10px] font-bold border border-sky-200">
                    <i class="fa-solid fa-shield-halved text-[9px]"></i>
                    <span>PERMANEN (JRA-KEMENKEU)</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-200">
                    <i class="fa-regular fa-trash-can text-[9px]"></i>
                    <span>MUSNAH (JADWAL RETENSI)</span>
                </span>
            @endif
        </div>

        {{-- Kanan: Tombol Hapus & Edit --}}
        <div class="flex items-center gap-2.5 flex-shrink-0">
            @if($currentRole === 'admin')
                <button type="button" 
                        onclick="openDeleteModal({{ json_encode($arsip) }})"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold transition-all cursor-pointer shadow-2xs">
                    <i class="fa-regular fa-trash-can text-xs"></i>
                    <span>Hapus</span>
                </button>
            @endif

            <a href="{{ route('arsip.edit', $arsip->id) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#0e2238] hover:bg-[#163557] text-white text-xs font-bold transition-all shadow-md shadow-slate-900/10 active:scale-95">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
                <span>Edit Data Arsip</span>
            </a>
        </div>
    </div>

    {{-- ═══ JADWAL RETENSI ARSIP (JRA) OTOMATIS ═══ --}}
    @if($arsip->jra)
        @php
            $faseKey = $arsip->fase_retensi;
            $faseWarna = [
                'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'inaktif' => 'bg-amber-50 text-amber-700 border-amber-200',
                'jatuh_tempo_musnah' => 'bg-rose-50 text-rose-700 border-rose-200',
                'serah_arsip_statis' => 'bg-sky-50 text-sky-700 border-sky-200',
                'perlu_dinilai_kembali' => 'bg-violet-50 text-violet-700 border-violet-200',
                'berkas_perseorangan' => 'bg-slate-100 text-slate-700 border-slate-200',
            ][$faseKey] ?? 'bg-slate-100 text-slate-700 border-slate-200';
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 mb-5">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Jadwal Retensi Arsip (JRA)</h3>
                    <p class="text-[11px] text-slate-400">{{ $arsip->jra->kode }} — {{ $arsip->jra->series }} · {{ $arsip->jra->kelompok }}</p>
                </div>
                <span class="px-3 py-1 rounded-full border text-[11px] font-bold {{ $faseWarna }}">{{ $arsip->fase_retensi_label }}</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-xs">
                <div><span class="block text-[10px] font-bold text-slate-400 uppercase">Masa Aktif</span><span class="font-bold text-slate-800">{{ $arsip->jra->aktif_tahun }} tahun</span></div>
                <div><span class="block text-[10px] font-bold text-slate-400 uppercase">Masa Inaktif</span><span class="font-bold text-slate-800">{{ $arsip->jra->inaktif_tahun }} tahun</span></div>
                <div><span class="block text-[10px] font-bold text-slate-400 uppercase">Menjadi Inaktif</span><span class="font-bold text-slate-800 font-mono">{{ $arsip->tanggal_inaktif?->translatedFormat('d M Y') ?? '-' }}</span></div>
                <div><span class="block text-[10px] font-bold text-slate-400 uppercase">Akhir Retensi</span><span class="font-bold text-slate-800 font-mono">{{ $arsip->tanggal_akhir_retensi?->translatedFormat('d M Y') ?? '-' }}</span></div>
                <div><span class="block text-[10px] font-bold text-slate-400 uppercase">Nasib Akhir (JRA)</span><span class="font-bold text-slate-800">{{ $arsip->jra->keterangan }}</span></div>
            </div>
            <p class="text-[11px] text-slate-400 mt-3">
                Dihitung dari: {{ $arsip->jra->pemicu_label }}
                ({{ ($arsip->tanggal_acuan_retensi ?? $arsip->tanggal_surat)?->translatedFormat('d M Y') }}).
                @if($arsip->jra->catatan) Catatan JRA: {{ $arsip->jra->catatan }}. @endif
            </p>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         ROW 3: 2-COLUMN GRID (Informasi Surat & Lokasi Fisik)
    ═══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

        {{-- ─────────────────────────────────────────────────
             KOLOM KIRI: INFORMASI SURAT (Lebar 2/3)
        ───────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            
            {{-- Header Card --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-[#0e2238] text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <i class="fa-regular fa-file-lines text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Informasi Surat</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian lembar kerja administrasi sesuai standar kearsipan</p>
                </div>
            </div>

            {{-- Body Metadata --}}
            <div class="p-6 space-y-5">
                
                {{-- Baris 1: Nomor Naskah & Kode Klasifikasi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">NOMOR NASKAH / SURAT DINAS</span>
                        <p class="font-mono font-extrabold text-slate-900 text-sm tracking-wide">{{ $arsip->kode_surat }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">KODE KLASIFIKASI ARSIP</span>
                        <p class="text-xs font-bold text-slate-800">
                            <span class="text-sky-600 font-mono">{{ $kodeKlasifikasiShort }}</span>
                            <span class="text-slate-600 font-normal"> - {{ $namaKlasifikasi }}</span>
                        </p>
                    </div>
                </div>

                {{-- Baris 2: Tanggal Naskah Surat & Perihal Surat --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">TANGGAL NASKAH SURAT</span>
                        <p class="text-xs font-semibold text-slate-800 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-sky-600 text-xs flex-shrink-0"></i>
                            <span>{{ $arsip->tanggal_surat ? $arsip->tanggal_surat->timezone('Asia/Jakarta')->translatedFormat('d F Y') : '-' }}</span>
                        </p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">PERIHAL SURAT</span>
                        <p class="text-xs font-bold text-slate-800 leading-snug">{{ $arsip->perihal }}</p>
                    </div>
                </div>

                {{-- Baris 3: Asal Naskah (Pihak Pengirim) --}}
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">ASAL NASKAH (PIHAK PENGIRIM)</span>
                    <p class="text-xs font-semibold text-slate-800 flex items-start gap-1.5 leading-snug">
                        <i class="fa-solid fa-landmark text-sky-600 text-xs mt-0.5 flex-shrink-0"></i>
                        <span>{{ $arsip->pengirim }}</span>
                    </p>
                </div>

                {{-- Baris 4: Tujuan Naskah (Pihak Penerima) --}}
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">TUJUAN NASKAH (PIHAK PENERIMA)</span>
                    <p class="text-xs font-semibold text-slate-800 flex items-start gap-1.5 leading-snug">
                        <i class="fa-solid fa-users text-sky-600 text-xs mt-0.5 flex-shrink-0"></i>
                        <span>{{ $arsip->penerima }}</span>
                    </p>
                </div>

                {{-- Baris 5: Tingkat Perkembangan & Jumlah Lampiran --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1 border-t border-slate-100">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">TINGKAT PERKEMBANGAN BERKAS</span>
                        <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $arsip->tingkat_perkembangan ?: 'Asli (Tersedia Berkas Fisik & Digital)' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">JUMLAH / KETEBALAN BERKAS</span>
                        <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $arsip->jumlah_lampiran ?: '1 Berkas (3 Lembar Dokumen Utama + 2 Lampiran)' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────
             KOLOM KANAN: LOKASI FISIK BERKAS (Lebar 1/3)
        ───────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            
            {{-- Header Card --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#0e2238] text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
                        <i class="fa-solid fa-box-archive text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">Lokasi Fisik Berkas</h3>
                        <p class="text-[10px] text-slate-400">Navigasi gudang kearsipan</p>
                    </div>
                </div>
                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
            </div>

            <div class="p-5 space-y-4">
                
                {{-- Depot Penyimpanan Box --}}
                <div class="p-3.5 rounded-xl bg-sky-50/70 border border-sky-100">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">DEPOT PENYIMPANAN:</span>
                    <p class="text-xs font-bold text-[#0e2238] flex items-center gap-1.5 truncate" title="{{ $arsip->depot_penyimpanan }}">
                        <i class="fa-solid fa-box-archive text-sky-600 text-xs flex-shrink-0"></i>
                        <span>{{ $arsip->depot_penyimpanan ?: 'Data Arsip Dinkes' }}</span>
                    </p>
                </div>

                {{-- 3 Coordinate Tiles Persis Foto --}}
                <div class="grid grid-cols-3 gap-2.5 text-center">
                    {{-- NOMOR RAK --}}
                    <div class="p-3 rounded-xl bg-sky-50/70 border border-sky-100 flex flex-col items-center justify-center">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">NOMOR RAK</span>
                        <p class="font-extrabold text-slate-900 text-sm font-mono tracking-tight">{{ $nomorRakOnly }}</p>
                    </div>

                    {{-- BARIS --}}
                    <div class="p-3 rounded-xl bg-sky-50/70 border border-sky-100 flex flex-col items-center justify-center">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">BARIS</span>
                        <p class="font-extrabold text-sky-600 text-sm font-mono tracking-tight">{{ $barisTkOnly }}</p>
                    </div>

                    {{-- NO. BOKS --}}
                    <div class="p-3 rounded-xl bg-sky-50/70 border border-sky-100 flex flex-col items-center justify-center">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">NO. BOKS</span>
                        <p class="font-extrabold text-slate-900 text-sm font-mono tracking-tight">{{ $nomorBoksOnly }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal Konfirmasi Hapus Data Arsip --}}
@include('arsip.partials.modal-delete')

@endsection
