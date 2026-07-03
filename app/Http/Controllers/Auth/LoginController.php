<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            // role dikirim dari toggle KPH/KRPH di form login (opsional, cuma utk UX validasi awal)
            'role'     => ['nullable', 'in:KPH,KRPH'],
        ]);

        $throttleKey = strtolower($credentials['username']).'|'.$request->ip();

        if (! Auth::attempt([
            'username'     => $credentials['username'],
            'password'     => $credentials['password'],
            'status_aktif' => true,
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => 'Username, password salah, atau akun tidak aktif.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // opsional: kalau role yg dipilih di toggle login tidak cocok dgn akun, tolak & logout lagi
        if ($credentials['role'] ?? null and $credentials['role'] !== $user->role) {
            Auth::logout();
            throw ValidationException::withMessages([
                'username' => 'Akun ini bukan akun '.$credentials['role'].'.',
            ]);
        }

        LogAktivitas::catat($user->id_user, 'Login ke sistem');

        return redirect()->intended(
            $user->isKph() ? route('kph.ringkasan') : route('krph.progres')
        );
    }

    public function destroy(Request $request)
    {
        if ($user = Auth::user()) {
            LogAktivitas::catat($user->id_user, 'Logout dari sistem');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
