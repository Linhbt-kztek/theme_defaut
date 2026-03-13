<?php

namespace App\Exports\WarningEvent;

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


class WarningEventExport  extends DefaultValueBinder implements FromView, WithColumnFormatting, WithCustomValueBinder, WithColumnWidths, WithStyles, WithDrawings, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $data;
    protected $row_header;
    protected $column_end;
    public function __construct(
        $data,
        $list_search,
    )
    {
        $this->data = $data;
        $this->row_header = 9;
        $this->column_end = "I";
    }

    public function view(): View
    {
       //  dd($this->list_search);

        return view('export.warning_event.warning_event_export', [
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
            'A' => 7,
            'B' => 35, //Nội dung đăng ký thi đấu
            'C' => 20, //Họ và tên VĐV
            'D' => 20, //Ảnh chân dung
            'E' => 20, //Giới tính
            'F' => 40, //Ngày/tháng/năm sinh
            'G' => 80, //Mã VĐV
            'H' => 20, //Chiều cao (cm)
            'I' => 25, //Chiều cao (cm)

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
                for ($i = 0; $i < count($this->data); $i++) {

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
