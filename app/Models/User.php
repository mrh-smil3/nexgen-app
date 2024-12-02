<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Website;
use App\Models\Subscription;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi dengan Subscriptions
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Relasi dengan Websites
    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }
// <<<<<<< HEAD
//     public function tokens()
//     {
//         return $this->morphMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable');
// =======
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
