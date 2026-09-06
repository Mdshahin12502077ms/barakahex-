<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BagParcel extends Model
{
    use HasFactory;

    public function bag()
    {
        return $this->belongsTo(Bag::class, 'bag_id');
    }

    public function parcel()
    {
        return $this->belongsTo(Parcel::class, 'percel_id');
    }
}
