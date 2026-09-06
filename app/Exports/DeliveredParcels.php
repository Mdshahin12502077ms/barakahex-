<?php

namespace App\Exports;

use App\Models\Parcel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeliveredParcels implements  FromView, WithTitle, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $id;
    protected $count;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function title(): string
    {
        return 'Delivered';
    }

    public function view(): View
    {
        $parcels = Parcel::where('merchant_id',$this->id)->where('status','delivered')->latest()->limit(8000)->get();
        $type = 'delivered_parcels';
        return view('admin.exports.parcels',compact('parcels','type'));
    }


    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
