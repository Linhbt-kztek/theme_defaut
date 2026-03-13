<?php

namespace App\Exports\Card;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class CardExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithEvents
{
    protected $data;
    protected $row_header;
    protected $column_end;
    
    public function __construct($cards = null)
    {
        $this->data = $cards ?? collect([
            ['0000000001', 'CARD000001'],
            ['0000000002', 'CARD000002'],
            ['0000000003', 'CARD000003'],
        ]);
        $this->row_header = 1;
        $this->column_end = "B";
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Mã số thẻ',
            'Tên thẻ'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, //Mã số thẻ
            'B' => 20, //Tên thẻ
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Font chữ toàn màn hình
                $event->sheet->getStyle('A1:' . $this->column_end . ($this->row_header + count($this->data)))->applyFromArray([
                    'font' => array(
                        'name' => 'Times New Roman',
                        'size' => 12,
                    )
                ]);

                // Chiều cao hàng tiêu đề
                $event->sheet->getDelegate()->getRowDimension($this->row_header)->setRowHeight(30);

                // Màu nền và border tiêu đề
                $event->sheet->getStyle('A' . $this->row_header . ':' . $this->column_end . $this->row_header)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ])->getAlignment()->setWrapText(true);

                // Nội dung data
                for ($i = 0; $i < count($this->data); $i++) {
                    // Chiều cao hàng
                    $event->sheet->getDelegate()->getRowDimension(($i + $this->row_header + 1))->setRowHeight(30);

                    // Border + color
                    $event->sheet->getStyle('A' . ($i + $this->row_header + 1) . ':' . $this->column_end . ($i + $this->row_header + 1))->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                        'font' => array(
                            'name' => 'Times New Roman',
                            'size' => 12
                        )
                    ])->getAlignment()->setWrapText(true);
                }
            },
        ];
    }
}
