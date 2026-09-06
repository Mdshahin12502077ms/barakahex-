<?php

namespace App\Exports;

use App\Models\Account\CompanyAccount;
use App\Models\Parcel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MerchantCashPaid implements  FromView, WithTitle, ShouldAutoSize, WithStyles
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
        return 'Paid to GreenX';
    }

    public function view(): View
    {
        $payments = CompanyAccount::where('merchant_id', $this->id)
                                    ->where('source', 'delivery_charge_receive_from_merchant')
                                    ->where('type', 'income')
                                    ->get();
        return view('admin.exports.paid-to-greenx',compact('payments'));
    }


    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
