<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;


class Website extends Model
{
    use HasFactory;
    use HasRoles;
    use HasApiTokens;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'domain_name'
    ];

    // Relasi dengan User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Subscription
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
