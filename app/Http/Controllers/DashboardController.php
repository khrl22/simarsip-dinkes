<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan user terautentikasi sesuai sesi/role yang aktif
        if (!auth()->check()) {
            $defaultRole = session('simulated_role', 'admin');
            $user = \App\Models\User::where('role', $defaultRole)->first() ?: \App\Models\User::first();
            if ($user) {
                auth()->login($user);
            }
        }

        Arsip::sinkronHarian();

        $activeQuery = Arsip::where('is_deleted', false);

        // Data statistik real dari database
        $totalArsip   = (clone $activeQuery)->count();
        $arsipAktif   = (clone $activeQuery)->where('status_arsip', 'Aktif')->count();
        $arsipInaktif = (clone $activeQuery)->where('status_arsip', 'Inaktif')->count();
        $arsipMusnah  = (clone $activeQuery)->where('kriteria_akhir', 'Musnah')->count();
        $arsipPermanen = (clone $activeQuery)->where('kriteria_akhir', 'Permanen')->count();
        $arsipJatuhTempoMusnah = (clone $activeQuery)->jatuhTempoMusnah()->count();

        // 5 Arsip Terakhir Ditambahkan (order by id desc agar data input real terbaru selalu tampil di atas)
        $recentArsip = Arsip::where('is_deleted', false)
            ->orderBy('id', 'desc')
            ->paginate(5)
            ->withQueryString();

        // Active role in session (defaults to authenticated user role)
        $currentRole = session('simulated_role', auth()->user()->role ?? 'admin');

        return view('dashboard.index', compact(
            'totalArsip',
            'arsipAktif',
            'arsipInaktif',
            'arsipMusnah',
            'arsipPermanen',
            'arsipJatuhTempoMusnah',
            'recentArsip',
            'currentRole'
        ));
    }
}
