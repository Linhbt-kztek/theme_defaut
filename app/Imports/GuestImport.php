<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuestImport implements WithStartRow, SkipsEmptyRows, WithValidation, ToCollection
{
    private $errors = [];
    private $data = [];

    public function collection(Collection $rows)
    {
        
        foreach ($rows as $index => $row) {

            $birthday = $row[2] ?? null;

            // Xử lý date an toàn
            $birthday = $this->convertExcelDate($row[2] ?? null, $index + 4);
            $gender = strtolower($row[3]);
            if ($gender == 'nam') {
                $gender = 1;
            } elseif ($gender == 'nữ') {
                $gender = 2;
            } else {
                $gender = 3;
            }

            $this->data[] = [
                'row' => $index + 4,

                'name' => !empty($row[1]) ? trim($row[1]) : null,

                'birthday' => $birthday,

                'gender' => $gender ?? null,

                'cccd' => !empty($row[4]) ? trim($row[4]) : null,

                'position' => !empty($row[5]) ? trim($row[5]) : null,

                'agencies_value' => !empty($row[6]) ? trim($row[6]) : null,

                'phone' => !empty($row[7]) ? trim($row[7]) : null,

                'note' => !empty($row[8]) ? trim($row[8]) : null,

            ];

            foreach ($rows as $index => $row) {
                if (empty($row[1])) {
                    $this->errors[] = "Dòng " . ($index + 4) . " cột 'name' trống";
                }
            }


        }
    }



    public function startRow(): int
    {
        return 4; // Bỏ qua header
    }

    public function rules(): array
    {
        return [

            '1' => 'required|string',     // name
            '3' => 'required|string|in:Nam,Nữ',     // gender
            '4' => 'required|string',     // cccd
            '5' => 'required|string',     // position
            '6' => 'required|string', // agency_name
        ];
    }

    public function customValidationMessages()
    {
        return [
            '1.required' => 'Cột "Họ và tên" không được để trống.',
            '1.string' => 'Cột "Họ và tên" phải là chuỗi ký tự.',

            '3.required' => 'Cột "Giới tính" không được để trống.',
            '3.string' => 'Cột "Giới tính" phải là chuỗi ký tự.',
            '3.in' => 'Cột "Giới tính" chỉ được phép là "Nam", "Nữ".',
            '4.required' => 'Cột "Căn cước công dân" không được để trống.',
            '4.string' => 'Cột "Căn cước công dân" phải là chuỗi ký tự.',

            '5.required' => 'Cột "Giới tính" không được để trống.',
            '5.string' => 'Cột "Giới tính" phải là chuỗi ký tự.',

            '6.required' => 'Cột "Đơn vị/Cơ quan" không được để trống.',
            '6.string' => 'Cột "Đơn vị/Cơ quan" phải là chuỗi ký tự.',
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

    private function convertExcelDate($value, $rowNumber)
    {
        if (empty($value)) {
            return null;
        }

        try {
             $value = trim($value);
            // Trường hợp lưu ngày dạng số (serial number)
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d 00:00:00');
            }

            // Trường hợp giá trị là chuỗi kiểm tra định dạng dd/mm/yyyy bằng regex
            if (is_string($value)) {
                if (!preg_match('/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/', $value)) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => "Cột 'Ngày sinh' phải có dạng dd/mm/yyyy (Ví dụ: 01/01/1999)"
                    ];
                    return null;
                }

                // dd/mm/yyyy
                $date = \DateTime::createFromFormat('d/m/Y', $value);

                if ($date !== false) {
                    return $date->format('Y-m-d 00:00:00');
                }
            }

            $this->errors[] = [
                'row' => $rowNumber,
                'message' => "Cột 'Ngày sinh' không đúng định dạng dd/mm/yyyy"
            ];

            return null;

        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $rowNumber,
                'message' => "Cột 'Ngày sinh' không hợp lệ"
            ];
            return null;
        }
    }
}
