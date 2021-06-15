<?php

namespace App\Models;

use App\Jobs\SendWelcomeEmailJob;
use App\Notifications\WelcomeNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot() {
        parent::boot();

        static::created(function(User $user) {
            SendWelcomeEmailJob::dispatch($user);
        });
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function setPasswordAttribute($value)
    {
        if($value != null) {
            $this->attributes['password'] = \Hash::make($value);
        }
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
