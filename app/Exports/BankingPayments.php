<?php

namespace App\Exports;

use App\Models\Account\MerchantWithdraw;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BankingPayments extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements FromView, WithTitle, ShouldAutoSize, WithStyles ,WithCustomValueBinder
{
    private $id;
    private $batch_no;
    private $batch_type;

    public function __construct(int $id, $batch_no,string $batch_type)
    {
        $this->id = $id;
        $this->batch_no = $batch_no;
        $this->batch_type = $batch_type;
    }

    public function view(): View
    {
        $withdraws  = MerchantWithdraw::where('withdraw_batch_id',$this->id)->get();
        $batch_type = $this->batch_type;
        return view('admin.exports.banking-payments',compact('withdraws','batch_type'));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return  $this->batch_no;
    }
}
