<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'reply_text',
        'is_internal_note',
    ];

    protected $casts = [
        'is_internal_note' => 'boolean',
    ];

    /**
     * Relationship with SupportTicket.
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Relationship with the replier (User).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Attachments for this reply.
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'reply_id');
    }
}
