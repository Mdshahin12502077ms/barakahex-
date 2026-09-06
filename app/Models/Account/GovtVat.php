<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Parcel;

class GovtVat extends Model
{
    use HasFactory;

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }
}
