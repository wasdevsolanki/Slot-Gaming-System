<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'reciever_id',
        'admin_id',
        'room_id',
        'staff_id',
        'type',
        'amount',
        'note',
    ];

    public function transfer()
    {
        return $this->hasOne(User::class, 'id', 'reciever_id');
    }

    public function transfer_by()
    {
        return $this->hasOne(User::class, 'id', 'staff_id');
    }
}    
