<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ResourceImport implements WithStartRow, SkipsEmptyRows, WithValidation, ToCollection
{
    private $errors = [];
    private $data = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if (!empty($row[2])) {
                $this->data[] = [
                    'start_time' => $row[0],
                    'end_time' => $row[1],
                    'content' => $row[2] ?? '',
                    'persion' => $row[3] ?? '',
                    'note' => $row[4] ?? ''
                ];
            }
        }
    }



    public function startRow(): int
    {
        return 3; // Bỏ qua header
    }

    public function rules(): array
    {
        return [

            // '1' => 'required|string',     // code
            // '2' => 'required|string|',     // name

            // '4' => 'required|string',     // cccd
            // '5' => 'required|string',     // position
            // '6' => 'required|string',     // agencies_value

            // '10' => 'required|string',     // user_name
            // '11' => 'required|string',     // password
        ];
    }

    public function customValidationMessages()
    {
        return [
            '1.required' => 'Cột "Mã thành viên" không được để trống.',
            '1.string' => 'Cột "Mã thành viên" phải là chuỗi ký tự.',

            '2.required' => 'Cột "Họ và tên" không được để trống.',
            '2.string' => 'Cột "Họ và tên" phải là chuỗi ký tự.',

            '4.required' => 'Cột "Căn cước công dân" không được để trống.',
            '4.string' => 'Cột "Căn cước công dân" phải là chuỗi ký tự.',


            '5.required' => 'Cột "Chức vụ" không được để trống.',
            '5.string' => 'Cột "Chức vụ" phải là chuỗi ký tự.',


            '6.required' => 'Cột "Đơn vị/Cơ quan" không được để trống.',
            '6.string' => 'Cột "Đơn vị/Cơ quan" phải là chuỗi ký tự.',


            '10.required' => 'Cột "Tên đăng nhập" không được để trống.',
            '10.string' => 'Cột "Tên đăng nhập" phải là chuỗi ký tự.',


            '11.required' => 'Cột "Mật khẩu" không được để trống.',
            '11.string' => 'Cột "Mật khẩu" phải là chuỗi ký tự.',
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
