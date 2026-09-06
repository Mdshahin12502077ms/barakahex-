<?php

namespace App\Exports;

use App\Models\Parcel;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReturnedParcelExport implements  WithHeadings, ShouldAutoSize, FromQuery, WithMapping, WithTitle, WithStyles
{
    protected $id;
    protected $count;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function query()
    {
        return Parcel::query()->where('merchant_id',$this->id)->where('status','returned-to-merchant');
    }

    public function map($row): array
    {
        $this->count += 1;
        return [
            [
                $this->count,
                $row->parcel_no,
                $row->parcel_type,
                $row->weight,
                $row->location,
                $row->total_delivery_charge,
                $row->payable,
                $row->return_charge,
                $row->price,
                $row->customer_name,
                $row->customer_invoice_no,
                $row->customer_phone_number,
                $row->customer_address,
                $row->status,
                $row->is_partially_delivered ? 'Partially Delivered' : '',
                $row->selling_price,
                $row->created_at,
            ],
        ];
    }

    public function headings(): array
    {
        return [
          '#',
          'Parcel ID',
          'Type',
          'Weight',
          'Location',
          'Charge',
          'Payable',
          'Return Charge',
          'COD',
          'Customer Name',
          'Invoice No',
          'Phone',
          'Address',
          'Status',
          'Is Partially Delivered',
          'Selling Price',
          'Created Date',
        ];
    }

    public function title(): string
    {
        return 'Returned';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
