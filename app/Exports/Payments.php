<?php

namespace App\Exports;

use App\Models\Account\MerchantWithdraw;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Payments implements  FromView, WithTitle, ShouldAutoSize, WithStyles
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
        return 'Payment';
    }

    public function view(): View
    {
        $payments = MerchantWithdraw::where('merchant_id',$this->id)->where('status','processed')->get();
        return view('admin.exports.payments',compact('payments'));
    }


    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
