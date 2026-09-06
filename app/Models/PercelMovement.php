<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PercelMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'parcel_id',
        'tracking_number',
        'from_branch_id',
        'to_branch_id',
        'sent_by',
        'received_by',
        'delivery_man_id',
        'sent_at',
        'received_at',
        'status',
        'manifest_no',
        'note',
    ];


    public function parcel()
    {
        return $this->belongsTo(Parcel::class, 'parcel_id');
    }

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id');
    }
}
