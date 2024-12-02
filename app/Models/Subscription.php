<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Website;

class Subscription extends Model
{
    use HasFactory;
    use HasRoles;
    use HasApiTokens;

    protected $fillable = [
        'user_id',
        'package_id',
        'transaction_id',
        'start_date',
        'end_date',
        'status'
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];

    // Generate transaction ID 
    protected static function booted()
    {
        static::creating(function ($subscription) {
            // Generate unique transaction ID
            // $timestamp = now()->format('Ymd');
            $randomPart = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $subscription->transaction_id = 'PR-' . $randomPart;
        });
    }

    // Relasi dengan User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Package
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    // Relasi dengan Payments
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Relasi dengan Websites
    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }
}
