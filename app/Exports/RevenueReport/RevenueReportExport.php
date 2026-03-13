<?php

namespace App\Exports\RevenueReport;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Log;


class RevenueReportExport extends DefaultValueBinder implements FromView, WithColumnFormatting, WithCustomValueBinder, WithColumnWidths, WithStyles, WithDrawings, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $data;
    protected $row_header;
    protected $column_end;
    public function __construct(
        $data
    ) {
        $this->data = $data;
        $this->row_header = 4;
        $this->column_end = "AG";
    }

    public function view(): View
    {
        return view('export.RevenueReport.revenue_report_export', [
            'data' => $this->data,
        ]);
    }

    public function columnFormats(): array
    {
        return [
            // 'B' => DataType::TYPE_STRING,
        ];
    }

    //vẽ ảnh
    public function drawings()
    {
        return [

        ];
    }

    //độ rộng các cột
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 10,
            'C' => 10,
            'D' => 10,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 10,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 10,
            'N' => 10,
            'O' => 10,
            'P' => 10,
            'Q' => 10,
            'R' => 10,
            'S' => 10,
            'T' => 10,
            'U' => 10,
            'V' => 10,
            'W' => 10,
            'X' => 10,
            'Y' => 10,
            'Z' => 10,
            'AA' => 10,
            'AB' => 10,
            'AC' => 10,
            'AD' => 10,
            'AE' => 10,
            'AF' => 10,
            'AG' => 30,
        ];

    }

    public function styles(Worksheet $sheet)
    {
        //hàng tiêu đề chữ đậm
        return [
            // Style the first row as bold text.
            $this->row_header => [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $startRow = $this->row_header;
                $courtsCount = count($this->data['courts']);
                $endRow = $startRow + $courtsCount + 1; // +1 cho dòng Doanh thu/ngày
    
                // Border toàn bộ bảng doanh thu theo ngày
                $event->sheet->getStyle(
                    "A{$startRow}:{$this->column_end}{$endRow}"
                )->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => '000000'],
                                ],
                            ],
                        ]);

                // Header ngày: căn giữa + bold
                $event->sheet->getStyle(
                    "A{$startRow}:{$this->column_end}{$startRow}"
                )->applyFromArray([
                            'font' => [
                                'bold' => true,
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);

                // Dòng tổng theo ngày (tfoot)
                $event->sheet->getStyle(
                    "A{$endRow}:{$this->column_end}{$endRow}"
                )->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'color' => ['argb' => 'FFFFFF'],
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => '004D40'],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                            ],
                        ]);

            }


        ];
    }
}
