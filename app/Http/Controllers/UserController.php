<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_user' => 'required|string|max:255|unique:users,nama_user',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string',
            'jabatan' => 'nullable|string',
        ]);

        User::create([
            'nama_user' => $request->nama_user,
            'password_hash' => Hash::make($request->password),
            'role' => $request->role,
            'jabatan' => $request->jabatan ?? '',
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama_user' => 'required|string|max:255|unique:users,nama_user,' . $user->id_user . ',id_user',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string',
            'jabatan' => 'nullable|string',
        ]);

        $data = [
            'nama_user' => $request->nama_user,
            'role' => $request->role,
            'jabatan' => $request->jabatan ?? '',
        ];

        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id_user == auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
