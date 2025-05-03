<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Admin; // Ensure the Admin class is imported
use Illuminate\Support\Facades\Hash;    


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *      
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type', // Add this line
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function employer()
    {
        return $this->hasOne(Employer::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if ($user->user_type === 'admin') {
                Admin::create([
                    'user_id' => $user->id,
                    'role' => 'admin'
                ]);
            } elseif ($user->user_type === 'employer') {
                Employer::create([
                    'user_id' => $user->id
                ]);
            }
        });

        static::updated(function ($user) {
            if ($user->isDirty('user_type')) {
                if ($user->user_type === 'admin' && !$user->admin) {
                    Admin::create([
                        'user_id' => $user->id,
                        'role' => 'admin'
                    ]);
                } elseif ($user->user_type === 'employer' && !$user->employer) {
                    Employer::create([
                        'user_id' => $user->id
                    ]);
                }
            }
        });
    }
}