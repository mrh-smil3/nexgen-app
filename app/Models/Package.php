<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;



class Package extends Model
{
    use HasFactory;
    use HasRoles;
    use HasApiTokens;


    protected $fillable = [
        'name',
        'description',
        'price',
        'duration'
    ];

    // Relasi dengan Subscriptions
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
