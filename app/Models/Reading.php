<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    use HasFactory;
    protected $fillable = [
        'machine_id',
        'employee_id',
        'admin_id',
        'room_id',
        'in_date',
        'out_date',
        'in',
        'out',
    ];

    public function machines()
    {
        return $this->hasMany(Machine::class, 'id', 'machine_id');
    }
}
