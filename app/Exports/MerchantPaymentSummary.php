<?php

namespace App\Exports;


use App\Models\Account\MerchantWithdraw;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class MerchantPaymentSummary implements ShouldAutoSize, FromQuery, WithTitle
{
    protected $id;
    protected $count = 0;
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function query()
    {
        return MerchantWithdraw::query()->where('merchant_id', $this->id)->where('status','processed');
    }

    public function title(): string
    {
        return 'Payment';
    }

    public function map($row): array
    {
        $this->count += 1;
        return [
            $this->count,
            $row->withdraw_id,
            date('M d, Y h:i a', strtotime($row->updated_at)),
            $row->amount,
            $row->status,
        ];
    }
}
