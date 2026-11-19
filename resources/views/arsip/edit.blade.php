@extends('layouts.app')

@section('title', 'Edit Data Arsip - ' . $arsip->kode_surat)

@section('content')
@php
    $rawKlasifikasi = $arsip->kode_klasifikasi ?? '';
    $kodeKlasif = str_contains($rawKlasifikasi, '-') ? trim(\Illuminate\Support\Str::before($rawKlasifikasi, '-')) : ($rawKlasifikasi ?: 'TU.01.00');
    $namaKlasif  = str_contains($rawKlasifikasi, '-') ? trim(\Illuminate\Support\Str::after($rawKlasifikasi, '-'))  : 'Tata Usaha dan Kearsipan';
    $isAktif     = $arsip->status_arsip === 'Aktif';
    $isPermanen  = $arsip->kriteria_akhir === 'Permanen';
@endphp

<form id="editArsipForm" action="{{ route('arsip.update', $arsip->id) }}" method="POST" class="space-y-0">
    @csrf

    {{-- ══════════════════════════════════════════════════════
         TOP: BREADCRUMB + BADGE STATUS + HEADER + ACTIONS
    ══════════════════════════════════════════════════════ --}}
    <div class="space-y-3 mb-5">

        {{-- Breadcrumb & Badge Row --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
            <div class="flex items-center gap-2 text-slate-500 font-medium">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-800 flex items-center gap-1 transition-colors">
                    <i class="fa-solid fa-table-cells-large text-[11px]"></i>
                    <span>Dashboard</span>
                </a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('arsip.index') }}" class="hover:text-slate-800 transition-colors">Data Arsip</a>
                <span class="text-slate-300">/</span>
                <span class="font-bold text-slate-800 font-mono">{{ $arsip->kode_surat }}</span>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border text-[11px] font-bold
                    {{ $isAktif ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $isAktif ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                    STATUS BERKAS: {{ strtoupper($arsip->status_arsip) }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border text-[11px] font-bold
                    {{ $isPermanen ? 'bg-sky-50 border-sky-200 text-sky-700' : 'bg-rose-50 border-rose-200 text-rose-700' }}">
                    <i class="fa-solid fa-shield-halved text-[9px]"></i>
                    Klasifikasi: {{ $kodeKlasif }} ({{ $arsip->kriteria_akhir }})
                </span>
            </div>
        </div>

        {{-- Page Title + Action Buttons Row --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-regular fa-pen-to-square text-base"></i>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight leading-tight">Edit Data Arsip</h2>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('arsip.show', $arsip->id) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-all">
                    <i class="fa-solid fa-arrow-left text-[11px]"></i>
                    <span>Batal / Kembali</span>
                </a>
                <button type="reset"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-all">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    <span>Reset Perubahan</span>
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#0e2238] hover:bg-[#163557] text-white text-xs font-bold transition-all shadow-md shadow-slate-900/10 active:scale-95">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Pembaruan Arsip</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         SECTION 1: IDENTITAS & METADATA NASKAH DINAS
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden mb-4">

        {{-- Section Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <span class="w-1.5 h-5 bg-[#0e2238] rounded-full inline-block flex-shrink-0"></span>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">1. Identitas &amp; Metadata Naskah Dinas</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Informasi / identitas surat masuk/keluar resmi instansi pemerintah</p>
                </div>
            </div>
            <span class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-lg">
                Wajib Diisi Lengkap
            </span>
        </div>

        {{-- Form Fields --}}
        <div class="p-6 space-y-5 text-xs">

            @if(isset($errors) && $errors->any())
            <div class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Row 1: Nomor Naskah, Tanggal, Kode Klasifikasi --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Nomor Berkas / Kode Naskah --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="font-bold text-slate-700">Nomor Berkas / Kode Naskah <span class="text-rose-500">*</span></label>
                        <span class="text-[10px] text-sky-600 font-bold bg-sky-50 border border-sky-200 px-1.5 py-0.5 rounded">Primary Index</span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-mono text-xs">#0/</span>
                        <input type="text"
                               name="kode_surat"
                               value="{{ old('kode_surat', $arsip->kode_surat) }}"
                               required
                               class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-mono font-semibold transition-all">
                    </div>
                </div>

                {{-- Tanggal Surat --}}
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">
                        Tanggal Surat / Penciptaan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="date"
                               name="tanggal_surat"
                               value="{{ old('tanggal_surat', $arsip->tanggal_surat ? (is_string($arsip->tanggal_surat) ? $arsip->tanggal_surat : $arsip->tanggal_surat->format('Y-m-d')) : '') }}"
                               required
                               class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 transition-all">
                    </div>
                </div>

                {{-- Kode Klasifikasi --}}
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">
                        Kode Klasifikasi: Masalah <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="kode_klasifikasi"
                           value="{{ old('kode_klasifikasi', $arsip->kode_klasifikasi) }}"
                           placeholder="Contoh: KU.01.02 - Keuangan & Anggaran"
                           required
                           class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-mono transition-all">
                </div>
            </div>

            {{-- JRA: status & kriteria otomatis --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                @include('arsip.partials.retensi-picker', ['jraList' => $jraList, 'selectedJra' => $arsip->jra_id, 'acuan' => optional($arsip->tanggal_acuan_retensi)->format('Y-m-d'), 'required' => false])
            </div>

            {{-- Row 2: Perihal --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="font-bold text-slate-700">Perihal / Ringkasan Isi Surat <span class="text-rose-500">*</span></label>
                    <span class="text-[10px] text-slate-400">Deskripsi: ringkas dan akurat</span>
                </div>
                <textarea name="perihal"
                          rows="2"
                          required
                          class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-medium resize-none transition-all">{{ old('perihal', $arsip->perihal) }}</textarea>
            </div>

            {{-- Row 3: Pengirim & Penerima --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">
                        Asal Naskah (Unit Pencipta / Pengirim) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-landmark absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text"
                               name="pengirim"
                               value="{{ old('pengirim', $arsip->pengirim) }}"
                               required
                               class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-medium transition-all">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">
                        Tujuan Naskah (Penerima / Distribusi) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-users absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text"
                               name="penerima"
                               value="{{ old('penerima', $arsip->penerima) }}"
                               required
                               class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-medium transition-all">
                    </div>
                </div>
            </div>

            {{-- Row 4: Status, Kriteria & Tahun Surat --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Tahun Surat --}}
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">
                        Tahun Surat <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-calendar-check absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="number"
                               name="tahun_surat"
                               value="{{ old('tahun_surat', $arsip->tahun_surat) }}"
                               required
                               class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-mono transition-all">
                    </div>
                </div>

                {{-- Status Arsip --}}
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">
                        Status Arsip <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-folder absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <select name="status_arsip" required class="w-full pl-9 pr-8 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-medium">
                            <option value="Aktif" {{ old('status_arsip', $arsip->status_arsip) === 'Aktif' ? 'selected' : '' }}>Aktif (Operasional Harian)</option>
                            <option value="Inaktif" {{ old('status_arsip', $arsip->status_arsip) === 'Inaktif' ? 'selected' : '' }}>Inaktif (Pusat Arsip)</option>
                        </select>
                    </div>
                </div>

                {{-- Kriteria Akhir --}}
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">
                        Kriteria Akhir Arsip <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-shield-halved absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <select name="kriteria_akhir" required class="w-full pl-9 pr-8 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-medium">
                            <option value="Permanen" {{ old('kriteria_akhir', $arsip->kriteria_akhir) === 'Permanen' ? 'selected' : '' }}>Permanen (Statis)</option>
                            <option value="Musnah" {{ old('kriteria_akhir', $arsip->kriteria_akhir) === 'Musnah' ? 'selected' : '' }}>Musnah (Retensi)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Hidden fields carried over --}}
            <input type="hidden" name="sifat_naskah" value="{{ $arsip->sifat_naskah }}">
            <input type="hidden" name="tingkat_perkembangan" value="{{ $arsip->tingkat_perkembangan }}">
            <input type="hidden" name="jumlah_lampiran" value="{{ $arsip->jumlah_lampiran }}">
            <input type="hidden" name="ringkasan_disposisi" value="{{ $arsip->ringkasan_disposisi }}">
            <input type="hidden" name="depot_penyimpanan" value="{{ $arsip->depot_penyimpanan }}">

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         STICKY BOTTOM BAR
    ══════════════════════════════════════════════════════ --}}
    <div class="sticky bottom-5 z-20 mb-4">
        <div class="bg-white/95 backdrop-blur-md border border-slate-200 rounded-2xl shadow-xl px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-slate-600">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse flex-shrink-0"></span>
                <span class="font-medium text-slate-700" id="changeCountInfo">Ada perubahan yang belum disimpan pada formulir.</span>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('arsip.show', $arsip->id) }}"
                   class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold transition-all">
                    Batal
                </a>
                <a href="{{ route('arsip.show', $arsip->id) }}"
                   class="px-3.5 py-2 rounded-lg border border-sky-200 bg-sky-50 text-sky-700 font-semibold hover:bg-sky-100 transition-all flex items-center gap-1.5">
                    <i class="fa-regular fa-eye text-xs"></i>
                    <span>Pratinjau Lembar Berkas</span>
                </a>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-[#0e2238] hover:bg-[#163557] text-white font-bold transition-all flex items-center gap-1.5 shadow-md shadow-slate-900/10 active:scale-95">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Pembaruan Arsip</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         SECTION 2: LOKASI FISIK BERKAS
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">

        {{-- 3 Coordinate Preview Tiles --}}
        <div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100">
            {{-- RAK --}}
            <div class="p-5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0 border border-sky-100">
                    <i class="fa-solid fa-warehouse text-xs"></i>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">RAK</span>
                    <strong class="text-sm font-extrabold text-slate-900 font-mono tracking-tight" id="previewRak">{{ $arsip->kode_rak }}</strong>
                </div>
            </div>

            {{-- TINGKAT / BARIS --}}
            <div class="p-5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0 border border-sky-100">
                    <i class="fa-solid fa-layer-group text-xs"></i>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">TINGKAT / BARIS</span>
                    <strong class="text-sm font-extrabold text-slate-900 font-mono tracking-tight" id="previewBaris">{{ $arsip->baris_tingkat }}</strong>
                </div>
            </div>

            {{-- KOTAK / BOKS --}}
            <div class="p-5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0 border border-sky-100">
                    <i class="fa-regular fa-box-archive text-xs"></i>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">KOTAK / BOKS</span>
                    <strong class="text-sm font-extrabold text-slate-900 font-mono tracking-tight" id="previewBoks">{{ $arsip->nomor_boks }}</strong>
                </div>
            </div>
        </div>

        {{-- Editable Input Fields --}}
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">
                    Nomor Rak <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-warehouse absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text"
                           id="inputRak"
                           name="kode_rak"
                           value="{{ old('kode_rak', $arsip->kode_rak) }}"
                           required
                           placeholder="Contoh: Rak A-02"
                           class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-mono transition-all">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">
                    Tingkat / Baris <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text"
                           id="inputBaris"
                           name="baris_tingkat"
                           value="{{ old('baris_tingkat', $arsip->baris_tingkat) }}"
                           required
                           placeholder="Contoh: B03 (Tingkat 3)"
                           class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-mono transition-all">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">
                    Nomor Boks <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-regular fa-box-archive absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text"
                           id="inputBoks"
                           name="nomor_boks"
                           value="{{ old('nomor_boks', $arsip->nomor_boks) }}"
                           required
                           placeholder="Contoh: Boks #14"
                           class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-mono transition-all">
                </div>
            </div>
        </div>
    </div>

</form>

<script>
    // Live-update coordinate preview tiles as user types
    const rak   = document.getElementById('inputRak');
    const baris = document.getElementById('inputBaris');
    const boks  = document.getElementById('inputBoks');

    rak.addEventListener('input',   () => { document.getElementById('previewRak').textContent   = rak.value   || '—'; });
    baris.addEventListener('input', () => { document.getElementById('previewBaris').textContent = baris.value || '—'; });
    boks.addEventListener('input',  () => { document.getElementById('previewBoks').textContent  = boks.value  || '—'; });

    // Track changed fields count
    const form = document.getElementById('editArsipForm');
    const originalValues = {};
    form.querySelectorAll('input:not([type=hidden]):not([type=submit]), textarea').forEach(el => {
        originalValues[el.name || el.id] = el.value;
    });

    function updateChangeCount() {
        let changed = 0;
        form.querySelectorAll('input:not([type=hidden]):not([type=submit]), textarea').forEach(el => {
            const key = el.name || el.id;
            if (el.value !== (originalValues[key] ?? '')) changed++;
        });
        const info = document.getElementById('changeCountInfo');
        if (changed > 0) {
            info.textContent = 'Ada ' + changed + ' field yang diubah. Simpan sebelum meninggalkan halaman.';
        } else {
            info.textContent = 'Belum ada perubahan pada formulir.';
        }
    }

    form.querySelectorAll('input:not([type=hidden]):not([type=submit]), textarea').forEach(el => {
        el.addEventListener('input', updateChangeCount);
        el.addEventListener('change', updateChangeCount);
    });
</script>

@endsection
