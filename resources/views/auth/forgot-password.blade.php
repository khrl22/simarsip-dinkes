<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Sistem Informasi Data Arsip</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f6f8fc;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4">

    <!-- FORGOT PASSWORD CARD -->
    <div class="w-full max-w-[420px] bg-white rounded-2xl shadow-xl border border-slate-200/90 overflow-hidden relative">
        <div class="h-1.5 bg-[#0f2942] w-full"></div>

        <div class="p-8">
            <!-- Logo & Heading -->
            <div class="text-center mb-6">
                <img src="{{ asset('images/logo-dinkes.png') }}" class="h-20 w-auto object-contain mx-auto mb-3 drop-shadow-xs" alt="Logo Dinas Kesehatan Kota Bandung">
                <span class="text-[10px] font-bold text-slate-500 tracking-wider uppercase block">Sistem Informasi Data Arsip</span>
            </div>

            <div class="mb-5">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Lupa Kata Sandi?</h2>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Masukkan Username / NIP akun petugas untuk meminta bantuan pemulihan akses.
                </p>
            </div>

            @if(session('success_message'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i>
                    <div>
                        <strong class="font-bold block">Permintaan Terkirim</strong>
                        <span class="text-[11px] leading-relaxed block mt-0.5">{{ session('success_message') }}</span>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('password.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Username / NIP <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-id-card-clip absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" 
                               name="username_or_nip" 
                               value="{{ old('username_or_nip', 'staff') }}" 
                               placeholder="Contoh: staff atau NIP" 
                               required
                               class="w-full pl-10 pr-3.5 py-2.5 text-xs rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-slate-800 transition-all font-medium">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Gunakan identitas akun yang terdaftar di sistem.</p>
                </div>

                <!-- Callout -->
                <div class="p-3.5 rounded-xl bg-sky-50/70 border border-sky-100 flex items-start gap-2.5 text-slate-600">
                    <i class="fa-solid fa-circle-info text-sky-600 text-sm mt-0.5 flex-shrink-0"></i>
                    <div class="text-[11px] leading-relaxed">
                        <strong class="text-slate-800 block font-semibold mb-0.5">Pemulihan melalui administrator</strong>
                        Hubungi administrator tata usaha dan sampaikan Username / NIP Anda. Pengaturan ulang kata sandi hanya dapat dilakukan oleh administrator sistem.
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-lg bg-[#0f2942] text-white text-xs font-bold hover:bg-[#153a5c] active:scale-[0.99] transition-all flex items-center justify-center gap-2 shadow-md shadow-slate-900/10">
                    <span>Minta Bantuan Administrator</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>

                <!-- Back Link -->
                <div class="pt-2 text-center">
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[11px]"></i>
                        <span>Kembali ke Halaman Masuk</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- FOOTER -->
    <p class="mt-6 text-xs text-slate-500 font-medium">
        © 2025 Sistem Informasi Data Arsip • <span class="text-sky-700 font-mono font-semibold">Versi Sederhana v1.0</span>
    </p>

</body>
</html>

