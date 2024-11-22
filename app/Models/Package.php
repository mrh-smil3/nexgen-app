<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;


class Package extends Model
{
    use HasFactory;
    use HasRoles;

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
