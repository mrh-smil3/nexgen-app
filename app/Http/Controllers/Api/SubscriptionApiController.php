<?php

namespace App\Http\Controllers\Api;

use App\Models\Subscription;
use App\Http\Controllers\Controller;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SubscriptionApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getSubscriptions()
    {

        $user = Auth::user(); // Mendapatkan user yang sedang login

        // Jika user adalah super-admin, tampilkan semua pengguna
        if ($user->hasRole('super-admin')) {
            $subscriptions = Subscription::all();
        } else {
            // Jika bukan super-admin, hanya tampilkan data pengguna yang sedang login            
            $subscriptions = Subscription::where('user_id', $user->id)->get();
            if ($subscriptions->isEmpty()) {
                return response()->json(['message' => 'Tidak ada subscription untuk pengguna ini'], 404);
            }
        }

        return response()->json($subscriptions);
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
