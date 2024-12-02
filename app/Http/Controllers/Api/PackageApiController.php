<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class PackageApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getPackages()
    {

        $user = Auth::user(); // Mendapatkan user yang sedang login

        // Jika user adalah super-admin, tampilkan semua pengguna
        if ($user->hasRole('super-admin')) {
            $packages = Package::all();
        } else {
            // Jika bukan super-admin, hanya tampilkan data pengguna yang sedang login
            $packages = Package::all();
        }

        return response()->json($packages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
