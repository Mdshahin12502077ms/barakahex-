<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'user_type',
        'merchant_id',
        'parcel_id',
        'tracking_number',
        'ticket_type',
        'subject',
        'description',
        'priority',
        'status',
        'assigned_to',
        'assigned_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
    ];

    /**
     * Relationship with the creator (User).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Merchant.
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Relationship with Parcel.
     */
    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }

    /**
     * Relationship with Assigned Staff.
     */
    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relationship with Ticket Replies.
     */
    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id')->orderBy('created_at', 'asc');
    }
      public function chat(){
        return $this->hasMany(User::class,'');
      }
    /**
     * Relationship with Ticket Attachments.
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'ticket_id');
    }

    /**
     * Auto-generate a formatted human-readable ticket ID.
     * Example: TCK-20260914-0001
     */
    public static function generateTicketId()
    {
        $prefix = 'TCK-' . date('Ymd') . '-';
        $latest = self::where('ticket_id', 'like', $prefix . '%')->latest('id')->first();

        if ($latest) {
            $number = intval(substr($latest->ticket_id, -4)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
