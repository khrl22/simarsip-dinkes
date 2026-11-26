<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Data Arsip Dinas Kesehatan Kota Bandung</title>
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

    <!-- LOGIN CONTAINER CARD -->
    <div class="w-full max-w-[420px] bg-white rounded-2xl shadow-xl border border-slate-200/90 overflow-hidden relative">
        <!-- Top decorative border strip -->
        <div class="h-1.5 bg-[#0f2942] w-full"></div>

        <div class="p-8">
            <!-- Header Section with Official Logo -->
            <div class="text-center mb-6">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest block mb-4">SISTEM INFORMASI DATA ARSIP</span>
                
                <!-- Logo Dinas Kesehatan Kota Bandung (Official Image) -->
                <div class="flex flex-col items-center justify-center mb-5">
                    <img src="{{ asset('images/logo-dinkes.png') }}" class="h-28 w-auto object-contain mx-auto drop-shadow-xs" alt="Logo Dinas Kesehatan Kota Bandung">
                </div>

                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Welcome!</h2>
            </div>

            <!-- ERROR ALERT BOX (Percobaan X dari 3) -->
            @if($errors->has('auth_failed'))
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm mt-0.5 flex-shrink-0"></i>
                    <div>
                        <strong class="font-bold block">Autentikasi Gagal</strong>
                        <span class="text-[11px] leading-relaxed block text-rose-700 mt-0.5">
                            {{ $errors->first('auth_failed') }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- FORM -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Username / NIP Field -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Username / NIP <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-id-card-clip absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" 
                               name="username" 
                               id="usernameInput" 
                               value="{{ old('username', 'bagianpengarsipan') }}" 
                               placeholder="Masukkan username" 
                               required
                               class="w-full pl-10 pr-3.5 py-2.5 text-xs rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-slate-800 placeholder:text-slate-400 transition-all font-medium">
                    </div>
                </div>

                <!-- Kata Sandi Field -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="password" 
                               name="password" 
                               id="passwordInput" 
                               value="dinkes321" 
                               placeholder="Masukkan kata sandi" 
                               required
                               class="w-full pl-10 pr-10 py-2.5 text-xs rounded-lg border {{ $errors->has('password') ? 'border-rose-400 ring-1 ring-rose-300' : 'border-slate-200' }} bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-slate-800 placeholder:text-slate-400 transition-all font-medium">
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i id="passwordToggleIcon" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                    @if($errors->has('password'))
                        <p class="text-[11px] text-rose-600 font-medium mt-1.5 flex items-center gap-1">
                            <span>✕</span> {{ $errors->first('password') }}
                        </p>
                    @endif
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="font-semibold text-sky-700 hover:text-sky-900 transition-colors">
                        Lupa Kata Sandi?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full mt-2 py-2.5 px-4 rounded-lg bg-[#0f2942] text-white text-xs font-bold hover:bg-[#153a5c] active:scale-[0.99] transition-all flex items-center justify-center gap-2 shadow-md shadow-slate-900/10">
                    <span>LOGIN</span>
                    <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                </button>
            </form>

            <!-- Petunjuk Akses Petugas -->
            <div class="mt-6 p-3.5 rounded-xl bg-sky-50/70 border border-sky-100 flex items-start gap-2.5 text-slate-600">
                <i class="fa-solid fa-circle-info text-sky-600 text-sm mt-0.5 flex-shrink-0"></i>
                <div class="text-[11px] leading-relaxed">
                    <strong class="text-slate-800 block font-semibold mb-0.5">Petunjuk Akses Petugas</strong>
                    Gunakan akun petugas tata usaha yang telah terdaftar oleh administrator sistem.
                </div>
            </div>

            <!-- Demo Quick-Fill Buttons -->
            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <span class="text-[10px] text-slate-400 block mb-1.5 font-medium">Akun Demo Tersedia:</span>
                <div class="flex items-center justify-center gap-2 text-[10px]">
                    <button type="button" 
                            onclick="fillCredentials('bagianpengarsipan', 'dinkes321')"
                            class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold border border-slate-200">
                        Admin (Bagian Pengarsipan)
                    </button>
                    <button type="button" 
                            onclick="fillCredentials('staf', 'dinkes321')"
                            class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold border border-slate-200">
                        Staf (Staf)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <p class="mt-6 text-xs text-slate-500 font-medium">
        © 2026 Sistem Informasi Data Arsip • <span class="text-sky-700 font-mono font-semibold">Versi v1.0</span>
    </p>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('passwordInput');
            const icon = document.getElementById('passwordToggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function fillCredentials(user, pass) {
            document.getElementById('usernameInput').value = user;
            document.getElementById('passwordInput').value = pass;
        }
    </script>
</body>
</html>

