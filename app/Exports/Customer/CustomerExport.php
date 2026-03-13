<?php

namespace App\Exports\Customer;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;

class CustomerExport implements
    FromCollection,
    WithHeadings,
    WithColumnWidths,
    WithStyles,
    WithEvents,
    WithColumnFormatting
{
    protected $data;
    protected $row_header;
    protected $column_end;

    public function __construct($cards = null)
    {
        $this->data = $cards ?? collect([
            ['1', 'Nguyễn Văn A', 'ID-0001', '090912903222',  'Hà Nội', '01/01/2025', 'Nam', '0000000001', 'nguyenvana@gmail.com', '00000001'],
        ]);
        $this->row_header = 1;
        $this->column_end = "J";
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
            'Mã hội viên (*)',
            'Căn cước công dân',
            'Địa chỉ',
            'Ngày sinh (*)',
            'Giới tính (*)',
            'Số điện thoại',
            'Email',
            'Mã thẻ chip',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 40,
            'C' => 20,
            'D' => 30,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 30,
            'J' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            $this->row_header => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'DAE9F8',
                    ],
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,  // Ngày sinh
            'H' => NumberFormat::FORMAT_TEXT,           // SĐT
            'I' => NumberFormat::FORMAT_TEXT,           // Email
            'J' => NumberFormat::FORMAT_TEXT,           // Mã thẻ chip
             'D' => NumberFormat::FORMAT_TEXT,          ///Căn cước công dân
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $totalRows = count($this->data);

                // Font chung
                $event->sheet->getStyle('A1:' . $this->column_end . ($this->row_header + $totalRows))
                    ->applyFromArray([
                        'font' => [
                            'name' => 'Times New Roman',
                            'size' => 12,
                        ]
                    ]);

                // Header style
                $event->sheet->getStyle('A' . $this->row_header . ':' . $this->column_end . $this->row_header)
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ]
                    ])
                    ->getAlignment()->setWrapText(true);

                $sheet->getRowDimension($this->row_header)->setRowHeight(30);

                // Data rows style
                for ($i = 0; $i < $totalRows; $i++) {
                    $rowIndex = $i + $this->row_header + 1;

                    $sheet->getRowDimension($rowIndex)->setRowHeight(30);
                    $event->sheet->getStyle("A$rowIndex:" . $this->column_end . "$rowIndex")
                        ->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['argb' => '000000'],
                                ],
                            ],
                            'font' => [
                                'name' => 'Times New Roman',
                                'size' => 12,
                            ]
                        ])
                        ->getAlignment()->setWrapText(true);
                }
            },
        ];
    }
}
