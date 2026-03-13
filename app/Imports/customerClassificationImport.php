<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;


class customerClassificationImport implements WithStartRow, SkipsEmptyRows, WithValidation, ToCollection
{

    public function model(array $row)
    {
    }

    public function collection(Collection $rows): void
    {
    }

    public function startRow(): int
    {
        return 3;
    }


    public function rules(): array
    {
        return [
            '1' => [
                'required',
                'number'
            ],
            '2' => [
                'required',
                'string'
            ],
            '3' => [
                'required',
                'number'
            ],
            '5' => [
                'required',
                'number'
            ]
        ];
    }


}
