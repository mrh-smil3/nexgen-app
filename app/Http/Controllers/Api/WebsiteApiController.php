<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Website;
use Illuminate\Http\Request;

class WebsiteApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getWebsites()
    {

        $user = Auth::user(); // Mendapatkan user yang sedang login

        // Jika user adalah super-admin, tampilkan semua pengguna
        if ($user->hasRole('super-admin')) {
            $websites = Website::all();
        } else {
            // Jika bukan super-admin, hanya tampilkan data pengguna yang sedang login            
            $websites = Website::where('user_id', $user->id)->get();
            if ($websites->isEmpty()) {
                return response()->json(['message' => 'Tidak ada Website untuk pengguna ini'], 404);
            }
        }

        return response()->json($websites);
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
