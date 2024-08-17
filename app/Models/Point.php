<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount',
        'bonus',
        'player_id',
        'room_id',
        'machine_id',
        'creator_id',
        'approval_id',
        'checkin',
        'checkout',
        'status',
    ];

    public function machine() {
        return $this->hasMany(Machine::class, 'id', 'machine_id');
    }

    public function player() {
        return $this->hasOne(Player::class, 'id', 'player_id');
    }

    public function employee() {
        return $this->hasOne(User::class, 'id', 'creator_id');
    }

    public function get_machine() {
        return $this->hasOne(Machine::class, 'id', 'machine_id');
    }
}
