<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'number_of_canons',
        'damage_canons',
        'strong_crew',
        'hp',
        'armament',
        'price'
    ];

    public function fleets()
    {
        return $this->belongsToMany(Fleet::class, "ships_fleets");
    }

    public function crew()
    {
        return $this->hasOne(Crew::class);
    }
}
