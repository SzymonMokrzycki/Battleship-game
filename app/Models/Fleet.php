<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'number_of_ships',
        'user_id'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function ships()
    {
        return $this->belongsToMany(Ship::class, "ships_fleets");
    }

    public function battle()
    {
        return $this->belongsTo(Battle::class);
    }
}
