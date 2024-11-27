<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;



class UserApiController extends Controller
{
    public function index()
    {
        // Return all users with their roles
        // return response()->json(User::with('roles')->get(), 200);
        $user = Auth::user(); // Mendapatkan user yang sedang login

        // Jika user adalah super-admin, tampilkan semua pengguna
        if ($user->hasRole('super-admin')) {
            $users = User::with('roles')->get();
        } else {
            // Jika bukan super-admin, hanya tampilkan data pengguna yang sedang login
            $users = User::with('roles')->where('id', $user->id)->get();
        }

        return response()->json($users);
    }

    public function store(Request $request)
    {
        // Periksa apakah user yang login adalah super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'roles' => 'required|array',
        ]);

        // Buat user baru
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Tambahkan roles ke user
        $roles = Role::whereIn('name', $validated['roles'])->get();
        $user->syncRoles($roles);

        // Kembalikan response
        return response()->json($user->load('roles'), 201);
    }

    public function show($id)
    {
        // $user = User::with('roles')->findOrFail($id);
        // return response()->json($user, 200);

        $user = Auth::user(); // Mendapatkan user yang sedang login

        // Cek apakah super-admin atau user yang sedang login bisa melihat data user lain
        if ($user->hasRole('super-admin') || $user->id == $id) {
            $userDetail = User::with('roles')->findOrFail($id);
            return response()->json($userDetail);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'roles' => 'sometimes|array',
        ]);

        $user->update([
            'name' => $validated['name'] ?? $user->name,
            'email' => $validated['email'] ?? $user->email,
            'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password,
        ]);

        if (isset($validated['roles'])) {
            $roles = Role::whereIn('name', $validated['roles'])->get();
            $user->syncRoles($roles);
        }

        return response()->json($user->load('roles'), 200);
    }

    public function destroy($id)
    {
        // Periksa apakah user yang login adalah super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
