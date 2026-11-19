<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Barcode - {{ $arsip->nomor_registrasi }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Libre+Barcode+39+Text&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-card {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="min-h-screen p-8 flex flex-col items-center justify-center">

    <!-- Top Action Bar -->
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()" class="px-5 py-2.5 rounded-lg bg-[#0d2847] text-white text-xs font-bold hover:bg-[#12365e] flex items-center gap-2 shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Cetak Label Fisik</span>
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50">
            Tutup
        </button>
    </div>

    <!-- PRINTABLE BARCODE LABEL (Standard ANRI Box Label Format) -->
    <div class="print-card w-[520px] bg-white rounded-2xl border-2 border-slate-800 p-6 shadow-xl space-y-4">
        
        <!-- Header Instansi -->
        <div class="flex items-center justify-between border-b-2 border-slate-800 pb-3">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-dinkes.png') }}" class="h-10 w-auto object-contain" alt="Logo Dinkes">
                <div>
                    <h3 class="font-extrabold text-xs tracking-tight uppercase text-slate-900 leading-tight">DINAS KESEHATAN KOTA BANDUNG</h3>
                    <p class="text-[9px] font-semibold text-slate-600">SISTEM INFORMASI TATA KELOLA KEARSIPAN ELEKTRONIK</p>
                </div>
            </div>
            <div class="text-right font-mono">
                <span class="text-[10px] px-2 py-0.5 rounded bg-slate-900 text-white font-bold block uppercase">{{ $arsip->status_arsip }}</span>
                <span class="text-[9px] text-slate-500 mt-0.5 block">{{ $arsip->kriteria_akhir }} (JRA)</span>
            </div>
        </div>

        <!-- Metadata -->
        <div class="space-y-1.5 text-xs">
            <div class="flex">
                <span class="w-32 text-slate-500 text-[11px] font-semibold">Nomor Registrasi</span>
                <span class="font-mono font-bold text-slate-900">: {{ $arsip->nomor_registrasi }}</span>
            </div>
            <div class="flex">
                <span class="w-32 text-slate-500 text-[11px] font-semibold">Nomor Naskah</span>
                <span class="font-mono font-bold text-slate-900">: {{ $arsip->kode_surat }}</span>
            </div>
            <div class="flex">
                <span class="w-32 text-slate-500 text-[11px] font-semibold">Perihal</span>
                <span class="font-medium text-slate-800 flex-1 leading-snug">: {{ $arsip->perihal }}</span>
            </div>
            <div class="flex">
                <span class="w-32 text-slate-500 text-[11px] font-semibold">Pengirim</span>
                <span class="text-slate-700 flex-1">: {{ $arsip->pengirim }}</span>
            </div>
            <div class="flex">
                <span class="w-32 text-slate-500 text-[11px] font-semibold">Tahun / Tanggal</span>
                <span class="font-medium text-slate-800">: {{ $arsip->tahun_surat }} • {{ $arsip->tanggal_surat ? $arsip->tanggal_surat->format('d/m/Y') : '-' }}</span>
            </div>
        </div>

        <!-- Coordinates Box -->
        <div class="p-3 bg-slate-50 border border-slate-300 rounded-xl grid grid-cols-3 gap-2 text-center">
            <div>
                <span class="text-[9px] uppercase font-bold text-slate-500 block">KODE RAK</span>
                <strong class="font-mono text-sm text-slate-900">{{ $arsip->kode_rak }}</strong>
            </div>
            <div>
                <span class="text-[9px] uppercase font-bold text-slate-500 block">BARIS / TK</span>
                <strong class="font-mono text-sm text-slate-900">{{ $arsip->baris_tingkat }}</strong>
            </div>
            <div>
                <span class="text-[9px] uppercase font-bold text-slate-500 block">NOMOR BOKS</span>
                <strong class="font-mono text-sm text-slate-900">{{ $arsip->nomor_boks }}</strong>
            </div>
        </div>

        <!-- Simulated Barcode -->
        <div class="text-center pt-2">
            <svg class="h-12 mx-auto w-72" viewBox="0 0 200 40">
                @for($x = 10; $x < 190; $x += rand(2, 5))
                    <line x1="{{ $x }}" y1="2" x2="{{ $x }}" y2="38" stroke="#000000" stroke-width="{{ rand(1, 3) }}" />
                @endfor
            </svg>
            <p class="font-mono text-xs font-bold tracking-widest text-slate-800 mt-1">*{{ $arsip->uuid }}*</p>
        </div>

        <div class="pt-2 border-t border-slate-200 text-[9px] text-slate-400 flex justify-between">
            <span>Dicetak oleh: {{ auth()->user()->name ?? 'Petugas Tata Usaha' }}</span>
            <span>Verifikasi ANRI • {{ date('d-m-Y H:i') }}</span>
        </div>
    </div>
</body>
</html>

