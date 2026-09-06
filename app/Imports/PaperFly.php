<?php

namespace App\Imports;

use App\Models\DistrictZila;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;


class PaperFly implements  ToCollection, WithHeadingRow, WithChunkReading, SkipsEmptyRows, SkipsOnError, WithValidation
{
    use Importable, SkipsErrors;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row){
            DistrictZila::create([
                'point_code'         => $row['point_code'],
                'point_name'         => $row['point_name'],
                'union_para_name'    => $row['union_para_name'],
                'thana_name'         => $row['thana_name'],
                'district_name'      => $row['district_name'],
            ]);
        }
    }

    public function rules(): array
    {
        return [

        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
