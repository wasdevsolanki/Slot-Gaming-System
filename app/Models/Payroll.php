<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;
    protected $fillable = [
        'staff_id',
        'checkin',
        'checkout',
        'hourly',
        'room_id',
        'status',
    ];
}
