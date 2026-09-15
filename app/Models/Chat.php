<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'ticket_id',
        'replies_staff_id',
        'conversation_id',
        'is_read',
        'message',
        'image',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id', 'id');
    }

    public function repliesStaff()
    {
        return $this->belongsTo(User::class, 'replies_staff_id', 'id');
    }

    public function chatimage()
    {
        return $this->hasMany(ChatImage::class, 'chat_id', 'id');
    }
}
