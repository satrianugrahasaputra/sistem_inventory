<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest('id')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,staff,manager'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        $user = User::create($validated);
        AuditLog::record('create_user', 'User', $user->id, null, ['name' => $user->name, 'email' => $user->email, 'role' => $user->role]);

        return back()->with('success', "Pengguna '{$user->name}' berhasil dibuat.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:admin,staff,manager'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['boolean'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $old = ['name' => $user->name, 'email' => $user->email, 'role' => $user->role, 'is_active' => $user->is_active];
        $user->update($validated);
        AuditLog::record('update_user', 'User', $user->id, $old, $user->only(['name', 'email', 'role', 'is_active']));

        return back()->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        AuditLog::record('toggle_user_status', 'User', $user->id, null, ['is_active' => $user->is_active]);

        return back()->with('success', "Akun '{$user->name}' berhasil {$status}.");
    }
}
