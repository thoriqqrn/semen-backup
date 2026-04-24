<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Pastikan ini diimport

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Menangani proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/admin/dashboard'); // Arahkan ke dashboard admin
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Menangani proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Arahkan kembali ke halaman utama
    }

    // --- Opsional: Untuk Registrasi Admin Pertama Kali ---
    // Biasanya admin dibuat manual atau via seed, tapi ini jika perlu form register
    public function showRegistrationForm()
    {
        // Untuk security, biasanya form register admin tidak dibuka publik
        // Hanya untuk development atau admin pertama kali.
        // Anda bisa menambahkan logic di sini untuk membatasi akses (misal: hanya jika belum ada admin)
        if (User::count() > 0 && !app()->isLocal()) { // Hanya izinkan register jika belum ada user di production
            return redirect('/login')->with('error', 'Registrasi admin tidak diizinkan.');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Akun admin berhasil dibuat! Silakan login.');
    }
}