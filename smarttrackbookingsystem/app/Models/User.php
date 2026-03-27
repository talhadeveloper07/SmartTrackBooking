<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

    public function businessAdminOf()
    {
        return $this->hasMany(\App\Models\BusinessAdmin::class);
    }
    public function customerProfile()
    {
        return $this->hasOne(\App\Models\Customer::class);
    }

    public function businessAdmin()
    {
        return $this->hasOne(BusinessAdmin::class, 'user_id');
    }

    public function business()
    {
        // This allows you to do $user->business and it will find it via the BusinessAdmin table
        return $this->hasOneThrough(
            Business::class,
            BusinessAdmin::class,
            'user_id',     // Foreign key on BusinessAdmin table
            'id',          // Foreign key on Business table
            'id',          // Local key on User table
            'business_id'  // Local key on BusinessAdmin table
        );
    }

}
