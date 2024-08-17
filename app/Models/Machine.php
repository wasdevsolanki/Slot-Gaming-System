<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;
    protected $fillable = [
        'serial',
        'type',
        'game',
        'room_id',
        'status',
    ];


    public function c_readings()
    {
        return $this->hasMany(Reading::class, 'machine_id');
    }
}
