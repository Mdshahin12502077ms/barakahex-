<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'reply_id',
        'file_path',
        'file_name',
        'file_type',
    ];

    /**
     * Relationship with SupportTicket.
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Relationship with TicketReply.
     */
    public function reply()
    {
        return $this->belongsTo(TicketReply::class, 'reply_id');
    }
}
