<?php

namespace App\Exports\Guest;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class GuestExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithEvents
{
    protected $data;
    protected $row_header;
    protected $column_end;

    public function __construct($cards = null)
    {
        $this->data = $cards ?? collect([
            ['1', 'Nguyễn Văn A', '01/01/1990', 'Nam', '012345678', 'Trưởng phòng', 'Công ty ABC', '0905123456', 'Khách VIP'],
            ['2', 'Nguyễn Thị B', '01/01/1990', 'Nữ', '012345679', 'Nhân viên', 'Công ty ABC', '0982390123', 'Khách VIP']
        ]);

        $this->row_header = 3; // Header bắt đầu từ hàng 3
        $this->column_end = "I"; // Tổng 8 cột
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Họ và tên (*)',
            'Ngày sinh',
            'Giới tính (*)',
            "Căn cước công dân/CMND (*)",
            'Chức vụ (*)',
            'Cơ quan/Đơn vị (*)',
            'Số điện thoại',
            'Ghi chú',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 20,
            'D' => 15,
            'E' => 35,
            'F' => 25,
            'G' => 40,
            'H' => 40,
            'I' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);


                /** --- Thêm dòng tiêu đề lớn ở hàng 1 --- **/
                $sheet->insertNewRowBefore(1, 2); // chèn 2 dòng trống để dời headings xuống
                $sheet->mergeCells('A1:' . $this->column_end . '1');
                $sheet->setCellValue('A1', 'DANH SÁCH KHÁCH MỜI');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'name' => 'Times New Roman',
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(35);

                /** --- Header (hàng 4 sau khi chèn) --- **/
                $headerRow = 3;
                $sheet->getStyle("A{$headerRow}:" . $this->column_end . "{$headerRow}")
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                            'name' => 'Times New Roman'
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'BDD7EE'],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                    ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(25);

                /** --- Thêm ghi chú dưới danh sách --- **/
                $noteRow = 2;
                $sheet->mergeCells("A{$noteRow}:" . $this->column_end . "{$noteRow}");
                $sheet->setCellValue("A{$noteRow}", "(*) là các trường thông tin bắt buộc");

                $sheet->getStyle("A{$noteRow}")
                    ->applyFromArray([
                        'font' => [
                            'italic' => true,
                            'size' => 12,
                            'name' => 'Times New Roman',
                            'color' => ['rgb' => 'FF0000'], // đỏ
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                $sheet->getRowDimension($noteRow)->setRowHeight(20);


                /** --- Dữ liệu (từ hàng 4 trở đi) --- **/
                $totalRows = count($this->data) + $headerRow;
                for ($i = 4; $i <= $totalRows; $i++) {
                    $sheet->getStyle("A{$i}:" . $this->column_end . "{$i}")
                        ->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => '000000'],
                                ],
                            ],
                            'font' => [
                                'bold' => false,
                                'name' => 'Times New Roman',
                                'size' => 12,
                            ],
                            'alignment' => [
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => true,
                            ],
                        ]);
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }

                /** --- Định dạng ngày sinh (cột B) dd/mm/yyyy --- **/
                $sheet->getStyle('B4:B' . $totalRows)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
            },
        ];
    }
}
