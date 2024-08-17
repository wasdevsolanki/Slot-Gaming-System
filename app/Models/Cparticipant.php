<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cparticipant extends Model
{
    use HasFactory;
    protected $table = 'conversation_participants';
    protected $fillable = [
        'conv_id', 
        'staff_id', 
        'admin_id'
    ];
    
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conv_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
