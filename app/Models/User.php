<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'banned_time',
        'banned_from',
        'banned_to',
        'avatar'
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
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }

    public function crew()
    {
        return $this->belongsTo(Crew::class);
    }

    public function battle()
    {
        return $this->hasMany(Battle::class);
    }

    public function equipment()
    {
        return $this->hasOne(Equipment::class);
    }

    public function worlds()
    {
        return $this->belongsToMany(World::class, 'world_users', 'user_id', 'world_id')->withTimestamps();
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function queryy()
    {
        return $this->belongsTo(Query::class);
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }
}
