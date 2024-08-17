<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'admin_id',
        'room_id',
        'reciever_id',
        '1',
        '5',
        '10',
        '20',
        '50',
        '100',
        'total',
        'note',
    ];

    public function staff()
    {
        return $this->hasMany(User::class, 'id', 'reciever_id');
    }
} 
