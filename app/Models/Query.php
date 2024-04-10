<?php

namespace App\Models;

use App\Enums\QueryStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Query extends Model
{
    use HasFactory;
    protected $fillable = [
        'sender_id',
        'sender_fleet',
        'sender',
        'status',
        'user_id'
    ];

    protected $casts = [
        'status' => QueryStatusEnum::class
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
