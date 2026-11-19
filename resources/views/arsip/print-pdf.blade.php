<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Arsip Surat - Dinas Kesehatan Kota Bandung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 11px; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body class="bg-white p-8 text-slate-900 font-sans">
    <div class="no-print mb-6 flex justify-between items-center bg-slate-100 p-4 rounded-xl border border-slate-200">
        <div>
            <h2 class="font-bold text-sm">Pratinjau Dokumen PDF</h2>
            <p class="text-xs text-slate-500">Gunakan dialog cetak browser untuk menyimpan sebagai PDF</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-[#0d2847] text-white text-xs font-bold rounded-lg shadow-xs hover:bg-[#153a5c]">
                Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-white border border-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-50">
                Tutup
            </button>
        </div>
    </div>

    <!-- KOP SURAT DINAS KESEHATAN KOTA BANDUNG -->
    <div class="flex items-center justify-center gap-6 border-b-2 border-slate-900 pb-4 mb-6">
        <img src="{{ asset('images/logo-dinkes.png') }}" class="h-16 w-auto object-contain flex-shrink-0" alt="Logo Dinkes">
        <div class="text-center">
            <h3 class="font-extrabold text-base uppercase tracking-wider">PEMERINTAH KOTA BANDUNG</h3>
            <h2 class="font-black text-xl uppercase tracking-tight text-[#0f2942]">DINAS KESEHATAN</h2>
            <p class="text-xs text-slate-600 mt-0.5">Dinas Kesehatan Kota Bandung, Jl. Supratman No.73, Cihapit, Kec. Bandung Wetan, Kota Bandung, Jawa Barat 40114</p>
            <p class="text-[11px] text-slate-500 font-medium">Sistem Informasi Tata Kelola Data Arsip Dinkes</p>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4 text-xs font-medium">
        <span>LAPORAN BUKU AGENDA DATA ARSIP</span>
        <span>Tanggal Cetak: {{ date('d F Y') }}</span>
    </div>

    <table class="w-full text-left border-collapse border border-slate-300 text-xs">
        <thead>
            <tr class="bg-slate-100 border-b border-slate-300 text-[10px] font-bold uppercase">
                <th class="p-2 border border-slate-300 text-center w-8">No</th>
                <th class="p-2 border border-slate-300">Kode Surat</th>
                <th class="p-2 border border-slate-300">Perihal</th>
                <th class="p-2 border border-slate-300">Tanggal</th>
                <th class="p-2 border border-slate-300">Pengirim</th>
                <th class="p-2 border border-slate-300">Penerima</th>
                <th class="p-2 border border-slate-300 text-center">Status</th>
                <th class="p-2 border border-slate-300 text-center">Letak Fisik</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @foreach($items as $idx => $item)
                <tr>
                    <td class="p-2 border border-slate-300 text-center font-mono">{{ $idx + 1 }}</td>
                    <td class="p-2 border border-slate-300 font-mono font-bold">{{ $item->kode_surat }}</td>
                    <td class="p-2 border border-slate-300 font-medium">{{ $item->perihal }}</td>
                    <td class="p-2 border border-slate-300">{{ $item->tanggal_surat ? $item->tanggal_surat->format('d/m/Y') : '-' }}</td>
                    <td class="p-2 border border-slate-300">{{ $item->pengirim }}</td>
                    <td class="p-2 border border-slate-300">{{ $item->penerima }}</td>
                    <td class="p-2 border border-slate-300 text-center font-semibold">{{ $item->status_arsip }}</td>
                    <td class="p-2 border border-slate-300 font-mono text-[10px] text-center">{{ $item->kode_rak }} / {{ $item->nomor_boks }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-8 flex justify-end text-xs">
        <div class="text-center w-64">
            <p>Bandung, {{ date('d F Y') }}</p>
            <p class="font-bold mt-1">Petugas Pengelola Kearsipan,</p>
            <div class="h-16"></div>
            <p class="font-bold underline">{{ auth()->user()->name ?? 'Petugas Tata Usaha' }}</p>
            <p class="text-slate-600 font-mono text-[11px]">NIP. {{ auth()->user()->nip ?? '198705122010121003' }}</p>
        </div>
    </div>
</body>
</html>

