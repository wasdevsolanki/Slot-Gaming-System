<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = [
        'player_id',
        'machine_id',
        'employee_id',
        'room_id',
        'point_id',
        'admin_id',
        'amount',
    ];

    public function player() {
        return $this->hasOne(Player::class, 'id', 'player_id');
    }

    public function machine() {
        return $this->hasOne(Machine::class, 'id', 'machine_id');
    }

    public function point() {
        return $this->hasOne(Point::class, 'id', 'point_id');
    }
    
    public function employee() {
        return $this->hasOne(User::class, 'id', 'employee_id');
    }

    public function admin() {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }


}
