<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements WithStartRow, SkipsEmptyRows, WithValidation, ToCollection
{
    private $errors = [];
    private $data = [];

    public function collection(Collection $rows)
    {

        foreach ($rows as $index => $row) {
            try {
                // Lấy dữ liệu thô từ Excel
                $birthday = $row[5] ?? null;


                // Xử lý date an toàn
                $birthday = $this->convertExcelDate($birthday);
                $gender = strtolower($row[6]) == 'nam' ? 1 : 2;

                $this->data[] = [
                    'row' => $index + 1,
                    'name' => $row[1] ?? '',
                    'customer_code' => $row[2],

                    'cccd' => trim($row[3]) ?? '',
                    'address' => $row[4] ?? '',
                    'birthday' => $birthday,
                    'gender' => $gender ?? 3,
                    'phone' => $row[7] ?? '',
                    'email' => $row[8] ?? '',

                    'code' => !empty($row[9])? $row[9] : null,
                    'card_number' => !empty($row[9])? str_pad(trim((string) $row[9]), 10, '0', STR_PAD_LEFT) : null,
                ];

            } catch (\Exception $e) {
                \Log::error("CardImport error at row {$index}: " . $e->getMessage());
                $this->errors[] = [
                    'row' => $index + 1,
                    'error' => $e->getMessage()
                ];
            }
        }
    }

    /**
     * Convert Excel date safely
     */
    private function convertExcelDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('d-m-Y');
            }

            // Nếu là string, thử nhiều định dạng
            if (is_string($value)) {
                $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'Y/m/d'];

                foreach ($formats as $format) {
                    $date = \DateTime::createFromFormat($format, $value);
                    if ($date) {
                        return $date->format('d-m-Y'); // Chuẩn hóa lại format output
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            \Log::warning("Date conversion failed for value: {$value}");
            return null;
        }
    }


    public function startRow(): int
    {
        return 2;
    }

    public function rules(): array
    {
        return [];
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
