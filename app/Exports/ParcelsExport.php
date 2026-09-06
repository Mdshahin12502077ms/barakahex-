<?php

namespace App\Exports;

use App\Models\Parcel;
use Maatwebsite\Excel\Concerns\FromCollection;

class ParcelsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Parcel::all();
    }
}
