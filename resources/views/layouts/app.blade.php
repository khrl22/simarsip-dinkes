<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi Data Arsip') - Dinas Kesehatan Kota Bandung</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            800: '#111e33',
                            850: '#0d182b',
                            900: '#091222',
                            950: '#050b16',
                        },
                        brand: {
                            blue: '#1652f0',
                            navy: '#0d2847',
                            soft: '#f0f5fc',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f6f8fc;
            color: #1e293b;
        }
        .sidebar-item-active {
            background-color: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            font-weight: 600;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased min-h-screen flex text-slate-800">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-[#0e2238] text-slate-300 flex-shrink-0 flex flex-col justify-between border-r border-slate-800/80 min-h-screen z-30 select-none">
        <div>
            <!-- Brand Header with Official Logo -->
            <div class="px-5 py-5 border-b border-slate-800/60 flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
                    <img src="{{ asset('images/logo-dinkes.png') }}" class="w-full h-full object-contain" alt="Logo Data Arsip">
                </div>
                <div class="min-w-0">
                    <h1 class="text-white font-bold text-base tracking-tight leading-tight truncate">Data Arsip</h1>
                    <p class="text-[9px] text-sky-400 font-bold tracking-widest uppercase truncate">SISTEM INFORMASI</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="px-3 py-6 space-y-1.5">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'sidebar-item-active text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i class="fa-solid fa-table-cells-large text-base w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('arsip.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ (request()->routeIs('arsip.index') || request()->routeIs('arsip.show')) ? 'sidebar-item-active text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i class="fa-solid fa-box-archive text-base w-5 text-center"></i>
                    <span>Data Arsip</span>
                </a>

                <a href="{{ route('arsip.create') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('arsip.create') ? 'sidebar-item-active text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i class="fa-solid fa-file-circle-plus text-base w-5 text-center"></i>
                    <span>Tambah Arsip</span>
                </a>

                <a href="{{ route('laporan.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('laporan.*') ? 'sidebar-item-active text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i class="fa-solid fa-file-lines text-base w-5 text-center"></i>
                    <span>Laporan</span>
                </a>

                <button type="button" 
                        onclick="openLogoutModal()" 
                        class="w-full flex items-center gap-3.5 px-4 py-3 rounded-lg text-sm font-medium text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all text-left cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket text-base w-5 text-center"></i>
                    <span>Login / Keluar</span>
                </button>
            </nav>
        </div>

        <!-- User Profile Card in Sidebar Footer -->
        @php
            $activeRole = session('simulated_role', auth()->user()->role ?? 'admin');
            $activeRoleUser = \App\Models\User::where('role', $activeRole)->first() ?? auth()->user();
            $sidebarName = $activeRoleUser ? $activeRoleUser->name : ($activeRole === 'admin' ? 'Ati' : 'Staff');
            $sidebarIsAdmin = $activeRole === 'admin';
            $sidebarRole = $sidebarIsAdmin ? 'Administrator' : 'Staf Administrasi';
        @endphp
        <div class="p-3 border border-slate-800/80 m-3 rounded-xl bg-[#14263c] flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="relative flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-[#1b3453] border border-slate-700 flex items-center justify-center text-slate-300">
                        <i class="fa-solid fa-user text-xs"></i>
                    </div>
                    <span class="absolute bottom-0 right-0 w-2 h-2 rounded-full bg-emerald-400 ring-2 ring-[#0e2238]"></span>
                </div>
                <div class="truncate">
                    <p class="text-xs font-semibold text-white truncate leading-tight">
                        {{ $sidebarName }}
                    </p>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5 leading-tight">
                        {{ $sidebarRole }}
                    </p>
                </div>
            </div>
            <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">ONLINE</span>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto custom-scrollbar">

        <!-- TOP BAR -->
        <header class="h-16 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-20 shadow-xs">
            <!-- Left: Title or breadcrumb -->
            <div class="flex items-center gap-3">
                <span class="text-base font-bold text-slate-800 tracking-tight">Sistem Informasi Data Arsip</span>
            </div>

            <!-- Center: Search input -->
            <div class="w-80 md:w-96 relative">
                <form action="{{ route('arsip.index') }}" method="GET" class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           id="globalSearchInput"
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari arsip, klasifikasi, kode... ⌘K" 
                           class="w-full pl-9 pr-10 py-1.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all placeholder:text-slate-400">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] bg-white border border-slate-200 px-1.5 py-0.5 rounded text-slate-400 font-mono font-medium shadow-xs">⌘K</span>
                </form>
            </div>

            <!-- Right: Role Switcher & User Profile -->
            <div class="flex items-center gap-4">
                <!-- Interactive Role Switcher Dropdown (Persis Foto) -->
                <form action="{{ route('switch.role') }}" method="POST" class="inline-flex items-center text-xs">
                    @csrf
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-600">
                        <i class="fa-solid fa-shield-halved text-sky-600 text-xs"></i>
                        <span class="text-xs text-slate-500 font-medium">Mode Peran:</span>
                        <select name="role" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded px-2 py-0.5 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-sky-500 cursor-pointer">
                            <option value="admin" {{ session('simulated_role', 'admin') === 'admin' ? 'selected' : '' }}>Administrator (Akses Penuh)</option>
                            <option value="staf" {{ session('simulated_role') === 'staf' ? 'selected' : '' }}>Staf Administrasi (Akses Terbatas)</option>
                        </select>
                    </div>
                </form>

                <!-- User profile chip -->
                @php
                    $topbarRole   = session('simulated_role', auth()->user()->role ?? 'admin');
                    $isAdmin      = $topbarRole === 'admin';
                    $topbarUser   = \App\Models\User::where('role', $topbarRole)->first() ?? auth()->user();
                    $displayName  = $topbarUser ? $topbarUser->name : ($isAdmin ? 'Ati' : 'Staff');
                    $displayRole  = $isAdmin
                                        ? 'Administrator Sistem (Akses Kelola & Hapus)'
                                        : 'Staf Administrasi (Akses Terbatas)';
                @endphp
                <div class="flex items-center gap-2.5 pl-3 border-l border-slate-200">
                    <div class="text-right">
                        <span class="text-xs font-bold text-slate-800 leading-tight block">{{ $displayName }}</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ $isAdmin ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-700' }} tracking-wider leading-none uppercase inline-block mt-0.5">
                            {{ $isAdmin ? 'ADMINISTRATOR' : 'STAF' }}
                        </span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-[#0e2238] text-white flex items-center justify-center text-xs shadow-xs flex-shrink-0">
                        <i class="fa-solid fa-user text-xs"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- FLASH NOTIFICATION -->
        @if(session('success'))
            <div class="mx-8 mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-xs animate-fade-in">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-8 mt-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-sm shadow-xs animate-fade-in">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        <!-- PAGE CONTENT -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="py-4 px-8 border-t border-slate-200 bg-white text-xs text-slate-500 flex items-center justify-between">
            <p>© 2026 Sistem Informasi Data Arsip • Dinas Kesehatan Kota Bandung</p>
            <p class="font-mono text-[11px] text-slate-400">Versi v1.0 • Standar Kearsipan ANRI</p>
        </footer>
    </div>

    <!-- MODAL SUKSES TAMBAH DATA ARSIP (Persis Desain Foto) -->
    @if(session('just_added'))
        @php $data = session('just_added'); @endphp
        <div id="modalSuccessAdd" 
             onclick="if(event.target === this) closeSuccessModal()"
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 transition-all duration-200">
            <div class="bg-white rounded-3xl max-w-[540px] w-full shadow-2xl border border-slate-100 overflow-hidden relative p-8 transform transition-all duration-200 animate-scale-up">
                
                {{-- Tombol Close di Pojok Kanan Atas --}}
                <button type="button" 
                        onclick="closeSuccessModal()" 
                        class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 hover:bg-slate-100 w-8 h-8 rounded-full flex items-center justify-center transition-all cursor-pointer"
                        title="Tutup Modal (Esc)">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>

                {{-- Soft Blue Checkmark Circle --}}
                <div class="w-16 h-16 bg-[#e5f3fc] text-[#1a84db] rounded-full flex items-center justify-center mx-auto mb-4 shadow-xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                    </svg>
                </div>

                {{-- System Registration Badge Pill Persis Foto --}}
                <div class="text-center mb-3">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-50/80 border border-sky-200/80 text-[11px] text-slate-600 font-medium">
                        <i class="fa-regular fa-circle-check text-sky-600 text-xs"></i>
                        <span>NO. REGISTRASI SISTEM:</span>
                        <strong class="font-mono text-slate-900 font-bold">#{{ ltrim($data['registrasi'], '#') }}</strong>
                        <span class="text-slate-300">|</span>
                        <span class="inline-flex items-center gap-1 text-emerald-600 font-bold text-[10px]">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>OTOMATIS OLEH SISTEM</span>
                        </span>
                    </div>
                </div>

                {{-- Title & Subtitle --}}
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">Data Arsip Berhasil Ditambahkan!</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                        Metadata surat dan berkas digital telah terdaftar resmi dalam buku agenda kearsipan elektronik.
                    </p>
                </div>

                {{-- Ringkasan Detail Box --}}
                <div class="rounded-2xl border border-slate-200/90 p-5 bg-white space-y-4 mb-4">
                    {{-- Header Ringkasan --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 text-[10px] font-bold">
                        <span class="text-slate-400 uppercase tracking-wider">RINGKASAN DETAIL ARSIP</span>
                        <span class="text-slate-800 font-mono text-xs font-bold">AGENDA: {{ $data['agenda'] }}</span>
                    </div>

                    {{-- Baris 1: Kode Surat & Tanggal Surat --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-slate-400 block text-[11px] mb-0.5">Kode Surat</span>
                            <span class="font-bold text-slate-900 font-mono text-sm block">{{ $data['kode_surat'] }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px] mb-0.5">Tanggal Surat</span>
                            <span class="font-bold text-slate-900 text-sm block">{{ $data['tanggal'] }}</span>
                        </div>
                    </div>

                    {{-- Baris 2: Perihal Surat --}}
                    <div>
                        <span class="text-slate-400 block text-[11px] mb-0.5">Perihal Surat</span>
                        <p class="font-medium text-slate-800 text-xs sm:text-[13px] leading-snug">{{ $data['perihal'] }}</p>
                    </div>

                    {{-- Baris 3: Pengirim & Status --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-slate-400 block text-[11px] mb-0.5">Pengirim</span>
                            <span class="font-bold text-slate-900 text-xs sm:text-sm block truncate" title="{{ $data['pengirim'] }}">{{ $data['pengirim'] }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px] mb-0.5">Status &amp; Kriteria Retensi</span>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 text-[11px] font-semibold">
                                    {{ $data['status'] }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-600 border border-sky-200/60 text-[11px] font-semibold">
                                    {{ $data['kriteria'] }} (JRA)
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 4: Lokasi Letak Fisik Berkas --}}
                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-slate-400 block text-[11px] mb-1">Lokasi Letak Fisik Berkas</span>
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-800">
                            <i class="fa-solid fa-house-chimney text-sky-600 text-xs"></i>
                            <span>{{ $data['lokasi'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Waktu & Petugas Pencatat Bar Persis Foto --}}
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-600 mb-6">
                    <div class="flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-sky-600 text-xs"></i>
                        <span>Tercatat oleh <strong class="text-slate-800 font-semibold">{{ $data['creator'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-slate-400 text-xs"></i>
                            <span>Waktu Input: <strong class="text-slate-800 font-semibold">{{ $data['timestamp'] }}</strong></span>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-sky-50 text-sky-600 border border-sky-200 text-[10px] font-mono font-bold tracking-wider">
                            SERVER SYNC
                        </span>
                    </div>
                </div>

                {{-- Tombol Aksi Bawah: 2 Tombol Sesuai Desain --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('arsip.index') }}" 
                       class="flex-1 py-3 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-2xs">
                        <i class="fa-solid fa-bars-staggered text-slate-500 text-xs"></i>
                        <span>Lihat di Data Arsip</span>
                    </a>
                    <a href="{{ route('arsip.create') }}" 
                       class="flex-1 py-3 px-4 rounded-xl bg-[#0e2238] hover:bg-[#163557] text-white text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-md shadow-slate-900/10">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Tambah Arsip Lainnya</span>
                    </a>
                </div>
            </div>
        </div>

        <script>
            function closeSuccessModal() {
                const modal = document.getElementById('modalSuccessAdd');
                if (modal) {
                    modal.classList.add('opacity-0', 'pointer-events-none');
                    const card = modal.querySelector('div');
                    if (card) {
                        card.classList.add('scale-95');
                    }
                    setTimeout(() => {
                        modal.remove();
                    }, 200);
                }
            }

            // Keyboard shortcut ESC to close
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSuccessModal();
                }
            });
        </script>
    @endif

    {{-- ══════════════════════════════════════════════════════
         MODAL SUKSES EDIT / UPDATE DATA ARSIP (Desain sama dengan Tambah)
    ══════════════════════════════════════════════════════ --}}
    @if(session('just_updated'))
        @php $upd = session('just_updated'); @endphp
        <div id="modalSuccessUpdate"
             onclick="if(event.target === this) closeUpdateModal()"
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 transition-all duration-200">
            <div class="bg-white rounded-3xl max-w-[540px] w-full shadow-2xl border border-slate-100 overflow-hidden relative p-8 transform transition-all duration-200 animate-scale-up">

                {{-- Tombol Close --}}
                <button type="button"
                        onclick="closeUpdateModal()"
                        class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 hover:bg-slate-100 w-8 h-8 rounded-full flex items-center justify-center transition-all cursor-pointer"
                        title="Tutup Modal (Esc)">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>

                {{-- Amber Pencil Circle (Update icon) --}}
                <div class="w-16 h-16 bg-[#e5f3fc] text-[#1a84db] rounded-full flex items-center justify-center mx-auto mb-4 shadow-xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19.5 7.125L18 8.625"/>
                    </svg>
                </div>

                {{-- Pills Registrasi & Audit Ref --}}
                <div class="flex items-center justify-center gap-2 mb-5 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-sky-700 text-[11px] font-bold font-mono">
                        <i class="fa-solid fa-shield-halved text-sky-500 text-[10px]"></i>
                        REG: {{ $upd['registrasi'] ?? '-' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-[11px] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        PEMBARUAN TERKELOLA
                    </span>
                </div>

                {{-- Title --}}
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 text-center tracking-tight leading-tight mb-2">
                    Data Arsip Berhasil Diperbarui!
                </h3>
                <p class="text-xs sm:text-[13px] text-slate-500 text-center mb-6 leading-relaxed max-w-sm mx-auto">
                    Metadata surat dan lokasi fisik berkas telah diperbaharui dalam buku agenda kearsipan elektronik.
                </p>

                {{-- Detail Card --}}
                <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5 space-y-4 mb-5 text-xs">

                    {{-- Header detail: kode surat + nomor agenda --}}
                    <div class="flex items-start justify-between pb-3 border-b border-slate-100 gap-3">
                        <div>
                            <span class="text-slate-400 block text-[11px] mb-0.5">RINGKASAN DETAIL ARSIP</span>
                            <span class="font-mono font-extrabold text-slate-900 text-sm sm:text-base tracking-tight">{{ $upd['kode_surat'] ?? '-' }}</span>
                        </div>
                        <span class="font-mono text-[11px] text-slate-500 font-medium whitespace-nowrap pt-1">
                            AUDIT: <strong class="text-slate-700">{{ $upd['audit_ref'] ?? '-' }}</strong>
                        </span>
                    </div>

                    {{-- Baris 1: Kode Surat & Tanggal --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-slate-400 block text-[11px] mb-0.5">Kode Surat</span>
                            <span class="font-bold text-slate-900 font-mono text-xs sm:text-[13px]">{{ $upd['kode_surat'] ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px] mb-0.5">Tanggal Surat</span>
                            <span class="font-bold text-slate-900 text-xs sm:text-[13px]">{{ $upd['tanggal'] ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Baris 2: Perihal --}}
                    <div>
                        <span class="text-slate-400 block text-[11px] mb-0.5">Perihal Surat</span>
                        <p class="font-medium text-slate-800 text-xs sm:text-[13px] leading-snug">{{ $upd['perihal'] ?? '-' }}</p>
                    </div>

                    {{-- Baris 3: Pengirim & Status --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-slate-400 block text-[11px] mb-0.5">Pengirim</span>
                            <span class="font-bold text-slate-900 text-xs sm:text-sm block truncate" title="{{ $upd['pengirim'] ?? '' }}">{{ $upd['pengirim'] ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px] mb-0.5">Status &amp; Kriteria Retensi</span>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 text-[11px] font-semibold">
                                    {{ $upd['status'] ?? 'Aktif' }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-600 border border-sky-200/60 text-[11px] font-semibold">
                                    {{ $upd['kriteria'] ?? 'Permanen' }} (JRA)
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 4: Lokasi Fisik --}}
                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-slate-400 block text-[11px] mb-1">Lokasi Letak Fisik Berkas</span>
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-800">
                            <i class="fa-solid fa-house-chimney text-sky-600 text-xs flex-shrink-0"></i>
                            <span class="leading-snug">{{ $upd['lokasi'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Bar Waktu & Petugas --}}
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-600 mb-6">
                    <div class="flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-sky-600 text-xs"></i>
                        <span>Diperbarui oleh <strong class="text-slate-800 font-semibold">{{ $upd['creator'] ?? '-' }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-slate-400 text-xs"></i>
                            <span>Waktu Update: <strong class="text-slate-800 font-semibold">{{ $upd['timestamp'] ?? '-' }}</strong></span>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-sky-50 text-sky-600 border border-sky-200 text-[10px] font-mono font-bold tracking-wider">
                            SERVER SYNC
                        </span>
                    </div>
                </div>

                {{-- Tombol Aksi Bawah --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('arsip.index') }}"
                       class="flex-1 py-3 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-2xs">
                        <i class="fa-solid fa-bars-staggered text-slate-500 text-xs"></i>
                        <span>Lihat di Data Arsip</span>
                    </a>
                    <a href="{{ route('arsip.show', $upd['id'] ?? 0) }}"
                       class="flex-1 py-3 px-4 rounded-xl bg-[#0e2238] hover:bg-[#163557] text-white text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-md shadow-slate-900/10">
                        <i class="fa-solid fa-eye text-xs"></i>
                        <span>Lihat Detail Arsip</span>
                    </a>
                </div>
            </div>
        </div>

        <script>
            function closeUpdateModal() {
                const modal = document.getElementById('modalSuccessUpdate');
                if (modal) {
                    modal.classList.add('opacity-0', 'pointer-events-none');
                    const card = modal.querySelector('div');
                    if (card) card.classList.add('scale-95');
                    setTimeout(() => modal.remove(), 200);
                }
            }
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeUpdateModal();
            });
        </script>
    @endif

    <!-- MODAL SUKSES HAPUS DATA ARSIP (Image 5 batch 2) -->
    @if(session('just_deleted'))
        @php $delData = session('just_deleted'); @endphp
        <div id="modalSuccessDel" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all animate-scale-up">
                <div class="p-5 bg-emerald-50 border-b border-emerald-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Data Arsip Berhasil Dihapus</h4>
                            <p class="text-xs text-slate-600">Arsip surat dan relasi berkas digital telah sukses dieliminasi dari sistem secara permanen.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3 text-xs">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <span class="font-mono font-bold text-slate-800 text-sm"># KODE SURAT: {{ $delData['kode_surat'] }}</span>
                            <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-bold text-[10px] uppercase">Terhapus Permanen</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">PERIHAL SURAT</span>
                            <p class="font-semibold text-slate-800 mt-0.5">{{ $delData['perihal'] }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-200">
                            <div>
                                <span class="text-slate-400 block text-[11px]">ID REFERENSI AUDIT</span>
                                <span class="font-mono text-sky-700 font-semibold">{{ $delData['audit_ref'] }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">WAKTU PENGHAPUSAN</span>
                                <span class="font-medium text-slate-700">{{ $delData['waktu'] }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">OTORISATOR</span>
                            <span class="font-medium text-slate-800">{{ $delData['otorisator'] }}</span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-sky-50/70 border border-sky-100 text-xs">
                        <div class="flex items-center justify-between font-semibold text-slate-800">
                            <span class="flex items-center gap-1.5"><i class="fa-solid fa-warehouse text-sky-600"></i> Ruang Fisik & Lokasi Berkas</span>
                            <span class="text-emerald-700 font-bold text-[11px]">Lokasi Dikosongkan</span>
                        </div>
                        <p class="text-slate-600 text-[11px] mt-1">{{ $delData['lokasi_info'] }}</p>
                    </div>

                    <div class="p-3 rounded-lg bg-slate-100 text-[11px] text-slate-600 flex items-start gap-2">
                        <i class="fa-solid fa-circle-info text-sky-500 mt-0.5"></i>
                        <span><strong>Informasi Sistem:</strong> Pembaruan basis data selesai. Riwayat aksi ini telah tercatat dalam Log Audit Kearsipan sesuai regulasi tata kelola.</span>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                    <button onclick="document.getElementById('modalSuccessDel').remove()" class="px-5 py-2 rounded-lg bg-[#0d2847] text-white text-xs font-semibold hover:bg-[#12365e] transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Tutup & Selesai
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         MODAL VERIFIKASI LOG OUT (Persis Desain Foto)
    ═══════════════════════════════════════════ --}}
    @php
        $modalRole = session('simulated_role', auth()->user()->role ?? 'admin');
        $modalIsAdmin = $modalRole === 'admin';
        $modalUser = \App\Models\User::where('role', $modalRole)->first() ?? auth()->user();
        $modalName = $modalUser ? $modalUser->name : ($modalIsAdmin ? 'Ati' : 'Staff');
        $modalRoleText = $modalIsAdmin ? 'Administrator' : 'Staf Administrasi';
        $modalNip = $modalUser->nip ?? ($modalIsAdmin ? '19850412 201001 1 008' : '19920815 201503 1 002');
        $parts = explode(' ', trim($modalName));
        $initials = count($parts) >= 2 
            ? strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1))
            : strtoupper(substr($modalName, 0, 2));
    @endphp
    <div id="logoutModal" 
         onclick="if(event.target === this) closeLogoutModal()"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 hidden transition-all duration-200">
        <div class="bg-white rounded-3xl max-w-[480px] w-full shadow-2xl border border-slate-100 overflow-hidden relative p-8 transform transition-all duration-200 animate-scale-up">
            
            {{-- Handle indicator bar at top --}}
            <div class="w-10 h-1 rounded-full bg-slate-200 mx-auto mb-6"></div>

            {{-- Logout Icon in soft blue container --}}
            <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center mx-auto mb-4 border border-sky-100">
                <i class="fa-solid fa-arrow-right-from-bracket text-xl text-sky-600"></i>
            </div>

            {{-- Modal Title --}}
            <h3 class="text-xl font-extrabold text-slate-900 text-center tracking-tight">Log Out</h3>

            {{-- Subtitle / Description --}}
            <p class="text-xs text-slate-500 text-center mt-2 mb-6 leading-relaxed max-w-sm mx-auto">
                Sesi aktif Anda sebagai <strong class="text-slate-700 font-bold">{{ $modalRoleText }} ({{ $modalName }})</strong> akan diakhiri. Pastikan semua perubahan data arsip telah tersimpan dengan aman.
            </p>

            {{-- User Info Box --}}
            <div class="bg-sky-50/60 border border-sky-100/90 rounded-2xl p-4 flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#0e2238] text-white font-bold flex items-center justify-center text-xs font-mono flex-shrink-0 shadow-xs">
                        {{ $initials }}
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 leading-tight">{{ $modalName }}</h4>
                        <p class="text-[11px] font-mono text-slate-500 mt-0.5">NIP: {{ $modalNip }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white text-sky-700 border border-sky-200 text-xs font-semibold shadow-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                    <span>{{ $modalIsAdmin ? 'Admin' : 'Staf' }}</span>
                </span>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3">
                <button type="button" 
                        onclick="closeLogoutModal()"
                        class="flex-1 py-3 px-4 rounded-xl bg-[#e3edf9] hover:bg-[#d5e4f5] text-[#1c4573] font-semibold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Cancel</span>
                </button>
                
                <form action="{{ route('logout') }}" method="POST" class="flex-1 m-0">
                    @csrf
                    <button type="submit" 
                            class="w-full py-3 px-4 rounded-xl bg-[#0e2238] hover:bg-[#163557] text-white font-bold text-xs transition-all flex items-center justify-center gap-2 shadow-md shadow-slate-900/10 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        <span>Yes</span>
                    </button>
                </form>
            </div>

            {{-- Footer info --}}
            <p class="text-center text-[11px] text-slate-400 mt-6 font-medium">
                © 2026 Sistem Informasi Data Arsip • Versi v1.0
            </p>
        </div>
    </div>

    <!-- Modal Log Out JS & Global ⌘K Key Listener -->
    <script>
        function openLogoutModal() {
            const modal = document.getElementById('logoutModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogoutModal();
            }
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('globalSearchInput');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

