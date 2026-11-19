<?php

namespace App\Http\Controllers;

use App\Models\BantuanReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username atau NIP wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($request->input('username'));
        $password = $request->input('password');

        // Find user by username or NIP (case-insensitive for username)
        $user = User::whereRaw('LOWER(username) = ?', [strtolower($loginInput)])
            ->orWhere('nip', $loginInput)
            ->first();

        if (!$user) {
            return back()->withInput([
                'username' => $loginInput,
            ])->withErrors([
                'auth_failed' => 'Kredensial tidak ditemukan pada sistem.',
                'password' => 'Kata sandi tidak sesuai dengan akun petugas.',
            ])->with('attempt_count', 1);
        }

        if (!Hash::check($password, $user->password)) {
            $user->increment('failed_attempts');
            $attemptCount = min($user->failed_attempts, 3);

            return back()->withInput([
                'username' => $loginInput,
            ])->withErrors([
                'auth_failed' => "Kata sandi yang Anda masukkan salah. Silakan periksa kembali kredensial Anda (Percobaan {$attemptCount} dari 3).",
                'password' => 'Kata sandi tidak sesuai dengan akun petugas.',
            ])->with('attempt_count', $attemptCount);
        }

        // Reset failed attempts on success
        $user->update(['failed_attempts' => 0]);
        Auth::login($user, $request->boolean('remember'));

        // Default role in session
        session(['simulated_role' => $user->role]);

        // Store login time for logout confirmation page
        session(['login_at' => now()]);

        return redirect()->intended(route('dashboard'));
    }

    public function showLogoutConfirm(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $loginAt = session('login_at', now());

        // Hitung durasi sesi
        $durasiMenit = now()->diffInMinutes($loginAt);
        $jam = (int) floor($durasiMenit / 60);
        $menit = (int) ($durasiMenit % 60);

        if ($jam > 0) {
            $durasi = "{$jam} Jam {$menit} Menit";
        } elseif ($menit > 0) {
            $durasi = "{$menit} Menit";
        } else {
            $durasi = "Baru saja";
        }

        $waktuMasuk = ($loginAt instanceof \Carbon\Carbon)
            ? 'Hari ini, ' . $loginAt->setTimezone('Asia/Jakarta')->format('H:i') . ' WIB'
            : 'Hari ini, 08:30 WIB';

        // Initials dari nama user
        $nameParts = explode(' ', trim($user->name));
        $initials = strtoupper(
            count($nameParts) >= 2
                ? substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1)
                : substr($nameParts[0], 0, 2)
        );

        // Role label
        $simulatedRole = session('simulated_role', $user->role);
        $roleLabel = $simulatedRole === 'admin' ? 'Admin' : 'Staf';

        return view('auth.logout-confirm', compact('user', 'durasi', 'waktuMasuk', 'initials', 'roleLabel'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function submitForgotPassword(Request $request)
    {
        $request->validate([
            'username_or_nip' => 'required',
        ], [
            'username_or_nip.required' => 'Username atau NIP wajib diisi.',
        ]);

        BantuanReset::create([
            'username_or_nip' => $request->input('username_or_nip'),
            'status' => 'menunggu',
            'keterangan' => 'Permintaan reset sandi diajukan melalui portal masuk.',
        ]);

        return back()->with('success_message', 'Permintaan bantuan telah diteruskan ke Administrator Sistem. Silakan hubungi bagian Tata Usaha.');
    }

    public function switchRole(Request $request)
    {
        $targetRole = $request->input('role', 'admin');
        if (in_array($targetRole, ['admin', 'staf'])) {
            session(['simulated_role' => $targetRole]);
            $targetUser = User::where('role', $targetRole)->first();
            if ($targetUser) {
                Auth::login($targetUser);
            }
        }

        return back()->with('role_switched', "Mode peran dialihkan ke: " . ($targetRole === 'admin' ? 'Administrator (Akses Penuh)' : 'User / Staf (Tanpa Hapus)'));
    }
}

