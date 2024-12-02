<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getPayments()
    {

        $user = Auth::user(); // Mendapatkan user yang sedang login

        // Jika user adalah super-admin, tampilkan semua pengguna
        if ($user->hasRole('super-admin')) {
            $payments = Payment::with('subscription')->get();
        } else {
            // Pengguna lain hanya dapat melihat pembayaran berdasarkan subscription miliknya
            $payments = Payment::with('subscription')->whereHas('subscription', function ($subQuery) use ($user) {
                $subQuery->where('user_id', $user->id);
            })->get();

            // Jika pembayaran kosong, kembalikan pesan
            if ($payments->isEmpty()) {
                return response()->json(['message' => 'Tidak ada pembayaran untuk pengguna ini'], 404);
            }
        }

        return response()->json($payments);
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
