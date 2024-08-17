<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'room_id',
        'is_group'
    ];

    public function participants()
    {
        return $this->hasMany(Cparticipant::class, 'conv_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'conv_id');
    }
}
