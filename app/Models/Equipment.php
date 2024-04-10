<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;
    protected $table = 'equipments';
    protected $fillable = [
        'number_of_items',
        'user_id'
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, "equipments_items");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
