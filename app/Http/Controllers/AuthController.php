<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nama_user' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Custom authentication attempt because of custom password field
        $user = User::where('nama_user', $credentials['nama_user'])->first();

        if ($user && Hash::check($credentials['password'], $user->password_hash)) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'nama_user' => 'The provided credentials do not match our records.',
        ])->onlyInput('nama_user');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_user' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string'],
            'jabatan' => ['required', 'string'],
        ]);

        $user = User::create([
            'nama_user' => $validated['nama_user'],
            'password_hash' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'jabatan' => $validated['jabatan'],
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
