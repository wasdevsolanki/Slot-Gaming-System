<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'player',
        'set_player',
        'set_player_point',
        'machine',
        'winning',
        'reading',
        'chat',
        'raffle',
        'bank',
        'bank_all',
        'transaction',
        'staff',
        'setting',
    ];
}
