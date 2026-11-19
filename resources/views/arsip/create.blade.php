@extends('layouts.app')

@section('title', 'Tambah Data Arsip - Sistem Informasi Data Arsip')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- TOP ALERT FLASH (If redirected or informative) -->
    <div class="p-4 rounded-xl bg-sky-50 border border-sky-200/90 text-xs text-sky-900 flex items-start justify-between shadow-xs">
        <div class="flex items-start gap-3">
            <div class="w-6 h-6 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <strong class="font-bold block text-sm">Formulir Pencatatan Baru</strong>
                <p class="text-sky-700 text-xs mt-0.5">
                    Nomor registrasi berkas dan kode agenda induk akan digenerate otomatis secara berurutan dan tercatat pada audit trail ANRI.
                </p>
            </div>
        </div>
        <button onclick="this.parentElement.remove()" class="text-sky-400 hover:text-sky-700">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- MAIN FORM CARD -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        
        <!-- Header -->
        <div class="p-8 pb-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[11px] font-bold text-sky-600 uppercase tracking-widest block mb-1">FORMULIR PENCATATAN BARU</span>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Data Arsip</h2>
                <p class="text-xs text-slate-500 mt-1">Lengkapi metadata surat masuk atau keluar sesuai dengan tata naskah dinas resmi.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-mono font-bold text-xs border border-slate-200 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <span>{{ $autoRegNumber }}</span>
                </span>
            </div>
        </div>

        <form action="{{ route('arsip.store') }}" method="POST" class="p-8 space-y-8">
            @csrf

            {{-- Validation error display --}}
            @if(isset($errors) && $errors->any())
                <div class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2 mb-4">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- SECTION 1: METADATA SURAT -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kode Surat -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-800">
                            Kode Surat <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400 font-mono">Format: Klasifikasi/Unit/Tahun</span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-mono text-xs">#</span>
                        <input type="text" 
                               name="kode_surat" 
                               value="{{ old('kode_surat') }}" 
                               placeholder="Contoh: 005/TU/2024" 
                               required
                               class="w-full pl-8 pr-3.5 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-mono transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Pastikan kode klasifikasi sesuai dengan pedoman kearsipan unit kerja.</p>
                    @error('kode_surat') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Kepada Siapa / Penerima -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Kepada Siapa / Penerima <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" 
                               name="penerima" 
                               value="{{ old('penerima') }}" 
                               placeholder="Contoh: Kepala Bagian Tata Usaha" 
                               required
                               class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 transition-all font-medium">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Nama jabatan atau unit kerja yang dituju dalam disposisi.</p>
                    @error('penerima') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Perihal Surat -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Perihal Surat <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="perihal" 
                              rows="3" 
                              placeholder="Tuliskan uraian ringkas perihal atau inti isi surat..." 
                              required
                              class="w-full p-3 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 transition-all font-medium resize-none">{{ old('perihal') }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Ringkasan isi surat maksimal 2 kalimat jelas.</p>
                    @error('perihal') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tahun Surat & Tanggal Surat in row -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Tahun Surat <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-regular fa-calendar-check absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="number" 
                                   name="tahun_surat" 
                                   value="{{ old('tahun_surat', $currentYear) }}" 
                                   required
                                   class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-mono transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">Tahun anggaran operasional arsip dibuat.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Tanggal Surat <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-regular fa-calendar absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="date" 
                                   name="tanggal_surat" 
                                   value="{{ old('tanggal_surat', date('Y-m-d')) }}" 
                                   required
                                   class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 transition-all font-medium">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">Tanggal resmi yang tertera pada lembar fisik dokumen.</p>
                    </div>
                </div>

                <!-- Dari Siapa / Pengirim -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Dari Siapa / Pengirim <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-paper-plane absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" 
                               name="pengirim" 
                               value="{{ old('pengirim') }}" 
                               placeholder="Contoh: Biro Kepegawaian" 
                               required
                               class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 transition-all font-medium">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Instansi, divisi, atau nama pejabat asal surat.</p>
                </div>

                {{-- Status Aktif/Inaktif & Kriteria Musnah/Permanen dihitung otomatis dari JRA --}}
                @include('arsip.partials.retensi-picker', ['jraList' => $jraList, 'selectedJra' => null, 'acuan' => null, 'required' => true])
            </div>

            <!-- SECTION 2: LOKASI / LETAK FISIK BERKAS (Image 2 batch 1) -->
            <div class="pt-6 border-t border-slate-200">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-boxes-stacked text-sky-600 text-sm"></i>
                    <h3 class="text-sm font-bold text-slate-900">Lokasi / Letak Fisik Berkas</h3>
                </div>
                <p class="text-xs text-slate-500 mb-4">Tentukan koordinat penempatan fisik dokumen pada ruang penyimpanan arsip dinamis/statis.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Kode Rak -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-800">
                                Kode Rak <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] text-slate-400">Lemari / Rak</span>
                        </div>
                        <div class="relative">
                            <i class="fa-solid fa-warehouse absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" 
                                   name="kode_rak" 
                                   value="{{ old('kode_rak', 'Rak A-02') }}" 
                                   placeholder="Contoh: Rak A-02" 
                                   required
                                   class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-mono transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">Gedung arsip atau nomor unit rak.</p>
                    </div>

                    <!-- Baris / Tingkat -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-800">
                                Baris / Tingkat <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] text-slate-400">Ambalan</span>
                        </div>
                        <div class="relative">
                            <i class="fa-solid fa-layer-group absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" 
                                   name="baris_tingkat" 
                                   value="{{ old('baris_tingkat', 'Baris 03') }}" 
                                   placeholder="Contoh: Baris 03" 
                                   required
                                   class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-mono transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">Tingkat susun rak tempat berkas disimpan.</p>
                    </div>

                    <!-- Nomor Dus / Boks -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-800">
                                Nomor Dus / Boks <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] text-slate-400">Boks Arsip</span>
                        </div>
                        <div class="relative">
                            <i class="fa-regular fa-box-archive absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" 
                                   name="nomor_boks" 
                                   value="{{ old('nomor_boks', 'Boks #14') }}" 
                                   placeholder="Contoh: Boks #14" 
                                   required
                                   class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 text-slate-800 font-mono transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">Nomor identifikasi kotak/dus penampung.</p>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('arsip.index') }}" 
                   class="px-5 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-lg bg-[#0d2847] hover:bg-[#12365e] text-white text-xs font-bold transition-all shadow-md shadow-slate-900/10 active:scale-95 flex items-center gap-2">
                    <i class="fa-regular fa-floppy-disk text-xs"></i>
                    <span>Simpan Data</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

