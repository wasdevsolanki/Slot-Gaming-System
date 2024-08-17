<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'creator_id',
        'room_id',
        'email',
        'phone',
        'dl',
        'dob',
        'ssn',
        'gender',
        'profile',
        'document',
        'ref_id',
        'bonus',
        'address',
        'status',
    ];

    public function points()
    {
        return $this->hasMany(Point::class, 'player_id', 'id');
    }
    
}
