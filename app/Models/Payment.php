<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;


class Payment extends Model
{
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'subscription_id', 
        'amount', 
        'payment_date', 
        'payment_method', 
        'status'
    ];

    protected $dates = [
        'payment_date'
    ];

    // Relasi dengan Subscription
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
