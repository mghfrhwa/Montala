<?php

namespace App\Http\Controllers\Kph;

use App\Http\Controllers\Controller;
use App\Models\Rph;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('rph.bkph')->orderBy('role')->orderBy('nama')->get();
        $rphList = Rph::with('bkph')->orderBy('nama_rph')->get();

        return view('kph.user', compact('users', 'rphList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:user,username'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:KPH,KRPH'],
            'id_rph' => ['nullable', 'required_if:role,KRPH', 'exists:rph,id_rph'],
        ]);

        User::create([
            'nama' => $data['nama'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'id_rph' => $data['role'] === 'KRPH' ? $data['id_rph'] : null,
            'status_aktif' => true,
        ]);

        return redirect()->route('kph.user')->with('status', 'Pengguna baru berhasil ditambahkan.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status_aktif' => ! $user->status_aktif]);

        return redirect()->route('kph.user')->with('status', 'Status pengguna '.$user->nama.' berhasil diubah.');
    }
}
