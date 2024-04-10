<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class World extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'status',
        'created_at'
    ];

    public function sea()
    {
        return $this->belongsTo(Sea::class);
    }

    public function island()
    {
        return $this->belongsTo(Island::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'world_users', 'world_id', 'user_id')->withTimestamps();
    }
}
