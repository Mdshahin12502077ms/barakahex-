<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClosingReport extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements WithMultipleSheets, WithCustomValueBinder
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new Summary($this->id);
        $sheets[] = new Payments($this->id);
        $sheets[] = new DeliveredParcels($this->id);
        $sheets[] = new ReturnedParcels($this->id);
        $sheets[] = new PartiallyDeliveredParcels($this->id);
        $sheets[] = new MerchantCashPaid($this->id);
//        $sheets[] = new DeliveredParcelExport($this->id);
//        $sheets[] = new ReturnedParcelExport($this->id);

        return $sheets;
    }
}
