<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Room;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'gender',
        'phone',
        'profile',
        'platform',
        'location',
        'status',
    ];


    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_admin', 'admin_id', 'room_id');
    }

}
