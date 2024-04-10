<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    use HasFactory;
    protected $fillable = [
        'number_lost_ships',
        'result',
        'experience',
        'battle_points',
        'user_id',
        'fleet_id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function fleets()
    {
        return $this->hasMany(Fleet::class);
    }
}
