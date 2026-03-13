<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CardImport implements WithStartRow, SkipsEmptyRows, WithValidation, ToCollection
{
    private $errors = [];
    private $data = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
                $this->data[] = [
                    'row' => $index + 2,
                    'card_number' => !empty($row[1])? str_pad(trim((string) ($row[1] ?? '')), 10, '0', STR_PAD_LEFT): null,
                    'code' => !empty($row[0])? trim($row[0]) : null,
                ];
        }
    }



    public function startRow(): int
    {
        return 2; // Bỏ qua header
    }

    public function rules(): array
    {
        return [

        ];
    }



    public function getData()
    {
        return $this->data;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
