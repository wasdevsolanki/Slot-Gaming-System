<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class Room extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_id',
        'name',
        'start_date',
        'end_date',
        'status',
    ];

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'room_admin', 'room_id', 'admin_id');
    }   

    public function machines()
    {
        return $this->hasMany(Machine::class, 'room_id');
    }   

}
 