<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Out — Sistem Informasi Data Arsip</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #eef2f8;
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-[#eef2f8]">

    {{-- Main Card --}}
    <div class="w-full max-w-[480px] mx-4">

        {{-- Dark navy top bar --}}
        <div class="h-1.5 w-full rounded-t-md bg-[#0f2942]"></div>

        {{-- Card body --}}
        <div class="bg-white rounded-b-xl shadow-lg px-8 py-8">

            {{-- Drag handle visual --}}
            <div class="flex justify-center mb-6">
                <div class="w-10 h-1 rounded-full bg-slate-200"></div>
            </div>

            {{-- Icon --}}
            <div class="flex justify-center mb-5">
                <div class="relative">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-arrow-right-from-bracket text-2xl text-slate-700"></i>
                    </div>
                    {{-- Green dot indicator --}}
                    <span class="absolute bottom-1 right-1 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-white"></span>
                </div>
            </div>

            {{-- Title --}}
            <h1 class="text-center text-2xl font-bold text-slate-800 mb-2 tracking-tight">Log Out</h1>

            {{-- Subtitle --}}
            <p class="text-center text-sm text-slate-500 leading-relaxed mb-6 px-2">
                Sesi aktif Anda sebagai <span class="font-semibold text-slate-700">{{ $roleLabel === 'Admin' ? 'Administrator' : 'Staf' }}
                ({{ $user->name }})</span> akan diakhiri. Pastikan
                semua perubahan data arsip telah tersimpan dengan aman.
            </p>

            {{-- User Info Card --}}
            <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-4 mb-6">
                {{-- User row --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        {{-- Initials Avatar --}}
                        <div class="w-11 h-11 rounded-xl bg-[#0f2942] flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold tracking-wider">{{ $initials }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                            @if($user->nip)
                            <p class="text-xs text-slate-400 font-mono mt-0.5">NIP:&nbsp;{{ $user->nip }}</p>
                            @endif
                        </div>
                    </div>
                    {{-- Role Badge --}}
                    <span class="flex items-center gap-1.5 text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-full px-3 py-1">
                        <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                        {{ $roleLabel }}
                    </span>
                </div>

                {{-- Divider --}}
                <div class="border-t border-slate-200 mb-4"></div>

                {{-- Session info --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">
                            <i class="fa-regular fa-clock text-[9px]"></i>
                            Durasi Sesi
                        </div>
                        <p class="text-sm font-semibold text-slate-800">{{ $durasi }}</p>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">
                            <i class="fa-solid fa-arrow-right-to-bracket text-[9px]"></i>
                            Waktu Masuk
                        </div>
                        <p class="text-sm font-semibold text-slate-800">{{ $waktuMasuk }}</p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3">
                {{-- Cancel button --}}
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
                   class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm font-semibold text-slate-600 transition-all duration-150">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Cancel
                </a>

                {{-- Logout submit button --}}
                <form action="{{ route('logout') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-[#0f2942] hover:bg-[#1a3a5c] text-sm font-semibold text-white transition-all duration-150">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        Yes
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="mt-5 text-center">
        <p class="text-xs text-slate-400">
            &copy; 2026 Sistem Informasi Data Arsip &nbsp;&bull;&nbsp; Versi v1.0
        </p>
        <div class="flex items-center justify-center gap-3 mt-1.5 text-xs text-slate-400">
            <a href="#" class="flex items-center gap-1 hover:text-slate-600 transition-colors">
                <i class="fa-regular fa-circle-question text-[10px]"></i>
                Pusat Bantuan Teknis
            </a>
            <span class="text-slate-300">&bull;</span>
            <a href="#" class="flex items-center gap-1 hover:text-slate-600 transition-colors">
                <i class="fa-regular fa-clock text-[10px]"></i>
                Kebijakan <span class="text-blue-400">Retensi Sesi</span>
            </a>
        </div>
    </div>

</body>
</html>

