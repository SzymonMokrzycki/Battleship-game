<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'property',
        'category'
    ];

    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, "equipments_items");
    }
}
