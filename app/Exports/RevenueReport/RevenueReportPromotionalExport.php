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

use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Log;


class RevenueReportPromotionalExport extends DefaultValueBinder implements FromView, WithColumnFormatting, WithCustomValueBinder, WithColumnWidths, WithStyles, WithDrawings, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $data;
    protected $row_header;
    protected $column_end;
    protected $ticketType;
    protected $amount;
    protected $real_amount;
    protected $discount;

    public function __construct(
        $data,
        $promotionals,
        $amount,
        $real_amount,
        $discount
    ) {
        $this->data = $data;
        $this->promotionals = $promotionals;
        $this->amount = $amount;
        $this->real_amount = $real_amount;
        $this->discount = $discount;

        $this->row_header = 8;
        $this->column_end = "J";
    }

    public function view(): View
    {
        return view('export.RevenueReport.revene_report_promotional_export', [
            'data' => $this->data,
            'promotionals' => $this->promotionals,
            'amount' => $this->amount,
            'real_amount' => $this->real_amount,
            'discount' => $this->discount,
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
            'A' => 7,
            'B' => 30, //Tên chương trình
            'C' => 45, // Giá ưu đãi
            'D' => 25, //Ngày bắt đầu
            'E' => 25, //Ngày kết thúc
            'F' => 20, //Só lượng mã đã sử dụng
            'G' => 20, //Giới hạn sử dụng
            'H' => 20, //Tổng chiết khấu
            'I' => 20, //Tổng giá trị hóa đơn

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

                //font chữ toàn màn hình--------------
                $event->sheet->getStyle('A1:' . $this->column_end . ($this->row_header + count($this->data)))->applyFromArray([

                    'font' => array(
                        'name' => 'Times New Roman',
                        'size' => 12,
                        //  'bold'      =>  true
                    )

                ]);

                //chiều cao hàng tiêu đề-------------
                $event->sheet->getDelegate()->getRowDimension($this->row_header)->setRowHeight(30);
                //chiều cao hàng tiêu đề-------------
    
                //màu nền tiêu đề-----------------
                $event->sheet->getStyle('A' . $this->row_header . ':' . $this->column_end . $this->row_header)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ])->getAlignment()->setWrapText(true);
                //màu nền tiêu đề-----------------
    

                //nôi dung-----------------------------
                for ($i = 0; $i < (count($this->data) + 3); $i++) {

                    //từ A13 -U13 :.....
    
                    //chiêu cao hàng
                    $event->sheet->getDelegate()->getRowDimension(($i + $this->row_header + 1))->setRowHeight(30);

                    //border + color
                    $event->sheet->getStyle('A' . ($i + $this->row_header + 1) . ':' . $this->column_end . ($i + $this->row_header + 1))->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                        'font' => array(
                            'name' => 'Times New Roman',
                            'size' => 12,
                            //  'bold'      =>  true
                        )

                    ])->getAlignment()->setWrapText(true);
                }

            },
        ];
    }
}
