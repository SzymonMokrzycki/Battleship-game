<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'message',
        'likes',
        'user_id',
        'topic_id'
    ];

    public function topic()
    {
        return $this->hasOne(Topic::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
