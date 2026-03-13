<?php

namespace App\Exports\Order;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
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


class ReportOrderExport extends DefaultValueBinder implements FromView, WithColumnFormatting, WithCustomValueBinder, WithColumnWidths, WithStyles, WithDrawings, WithEvents
{
    /**
     * @return Collection
     */
    protected $data;
    protected $row_header;
    protected $column_end;
    public function __construct(
        $orders
    ) {
        $this->data = $orders;
        $this->row_header = 8;
        $this->column_end = "L";
    }

    public function view(): View
    {
        $this->getStatusInvoice();
        return view('export.order.report_order_export', [
            'data' => $this->data,
        ]);
    }

    /**
     * them trang thai tao hoa don
     * @return void
     */
    private function getStatusInvoice()
    {
        //thêm trạng thái, mau sac cua invoice
        for ($i = 0; $i < count($this->data); $i++) {

            if (intval($this->data[$i]->payment_status) == 2)
            {
                $statusInvoice = '';
                $styleInvoice ="";
                if (
                    ((int) @$this->data[$i]->bill[0]->status == 1 && (int) @$this->data[$i]->customerClassification->type != 3) ||
                    ((int) @$this->data[$i]->bill_success == 1 && (int) @$this->data[$i]->customerClassification->type == 3)
                ) {
                    $statusInvoice = "Đã tạo";
                    $styleInvoice = "color:#299CDB";
                } elseif (
                    ((int) @$this->data[$i]->bill[0]->status == 2 && (int) @$this->data[$i]->customerClassification->type != 3) ||
                    ((int) @$this->data[$i]->bill_success == 2 && (int) @$this->data[$i]->customerClassification->type == 3)
                ) {
                    // Tạo thất bại
                    $statusInvoice = 'Tạo lỗi';
                    $styleInvoice = "color:red";
                }

                $this->data[$i]["statusInvoice"] = $statusInvoice;
                $this->data[$i]["styleInvoice"] = $styleInvoice;

            }
        }
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
            'B' => 15, //Nội dung đăng ký thi đấu
            'C' => 16, //Họ và tên VĐV
            'D' => 16, //Ảnh chân dung
            'E' => 16, //Giới tính
            'F' => 35, //Ngày/tháng/năm sinh
            'G' => 20, //Mã VĐV
            'H' => 20, //Chiều cao (cm)
            'I' => 20, //Chiều cao (cm)
            'J' => 25, //Chiều cao (cm)
            'K' => 20, //Chiều cao (cm)
            'L' => 20, //Chiều cao (cm)
            'M' => 20
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

                //nôi dung-----------------------------
                for ($i = 0; $i < (count($this->data) + 1); $i++) {

                    //từ A13 -U13 :.....

                    //chiêu cao hàng
                    $event->sheet->getDelegate()->getRowDimension(($i + $this->row_header))->setRowHeight(30);

                    //border + color
                    $event->sheet->getStyle('A' . ($i + $this->row_header) . ':' . $this->column_end . ($i + $this->row_header))->applyFromArray([
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
