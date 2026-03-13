<?php

namespace App\Exports\Event;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;


class EntryEventExport extends DefaultValueBinder implements FromView, WithColumnFormatting, WithCustomValueBinder, WithColumnWidths, WithStyles, WithDrawings, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $data;
    protected $search_room_name;
    protected $row_header;
    protected $key_search;
    protected $column_end;
    public function __construct(
        $data
        ,
        $search_room_name
    ) {
        $this->data = $data;
        $this->search_room_name = $search_room_name;


        $this->row_header = 9;
        $this->column_end = "H";
    }

    public function view(): View
    {
        //  dd($this->data);

        return view('export.event.event_export', [
            'data' => $this->data,
            'search_room_name' => $this->search_room_name
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
            'B' => 40, //Họ và tên
            'C' => 20, //Phân loại
            'D' => 40, //Chức vụ
            'E' => 40, //Tên đươn vị cơ sở
            'F' => 20, //Phòng 
            'G' => 20, //Làn
            'H' => 30, //Thời gian

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
