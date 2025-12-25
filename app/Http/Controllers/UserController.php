<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get available roles based on current user's role.
     * Users can only create/edit users with lower privilege.
     */
    private function getAvailableRoles(): array
    {
        $currentUser = auth()->user();
        
        $allRoles = [
            'superuser' => 'Superuser',
            'admin' => 'Admin',
            'manajer' => 'Manajer',
            'staff' => 'Staff',
        ];
        
        // Filter roles based on current user's level
        $currentLevel = $currentUser->getRoleLevel();
        
        return collect($allRoles)->filter(function ($label, $role) use ($currentLevel) {
            $roleLevel = match($role) {
                'superuser' => 1,
                'admin' => 2,
                'manajer' => 3,
                'staff' => 4,
                default => 99,
            };
            // Only show roles with lower privilege (higher level number)
            return $roleLevel > $currentLevel;
        })->toArray();
    }

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->getAvailableRoles();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $availableRoles = array_keys($this->getAvailableRoles());
        
        $request->validate([
            'nama_user' => 'required|string|max:255|unique:users,nama_user',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:' . implode(',', $availableRoles),
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
        // Check if current user can edit this user
        if (!auth()->user()->canEditUser($user)) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengedit user ini.');
        }
        
        $roles = $this->getAvailableRoles();
        
        // Include current user's role in options if not already there
        if (!isset($roles[$user->role])) {
            $roleLabels = [
                'superuser' => 'Superuser',
                'admin' => 'Admin',
                'manajer' => 'Manajer',
                'staff' => 'Staff',
            ];
            $roles[$user->role] = $roleLabels[$user->role] ?? ucfirst($user->role);
        }
        
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Check if current user can edit this user
        if (!auth()->user()->canEditUser($user)) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengedit user ini.');
        }
        
        $availableRoles = array_keys($this->getAvailableRoles());
        // Include current role as valid option
        if (!in_array($user->role, $availableRoles)) {
            $availableRoles[] = $user->role;
        }
        
        $request->validate([
            'nama_user' => 'required|string|max:255|unique:users,nama_user,' . $user->id_user . ',id_user',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|in:' . implode(',', $availableRoles),
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
        // Cannot delete own account
        if ($user->id_user == auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        
        // Check if current user can edit (and thus delete) this user
        if (!auth()->user()->canEditUser($user)) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus user ini.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}

