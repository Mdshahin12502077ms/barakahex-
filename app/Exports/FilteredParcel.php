<?php

namespace App\Exports;

use App\Models\Parcel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use phpDocumentor\Reflection\Types\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FilteredParcel implements  FromView, WithTitle, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $parcels;

    public function __construct($parcels)
    {
        $this->parcels = $parcels;
    }

    public function title(): string
    {
        return 'Parcels';
    }

    public function view(): View
    {
        $parcels = $this->parcels->latest()->limit(8000)->get();
        return view('admin.exports.filtered-parcels',compact('parcels'));
    }


    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}

