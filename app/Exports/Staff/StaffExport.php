<?php

namespace App\Exports\staff;

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



class StaffExport extends DefaultValueBinder implements FromView, WithColumnFormatting,WithCustomValueBinder, WithColumnWidths,  WithStyles, WithDrawings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $staffs;
    protected $ar_tournamentCompetition;

    protected $row_header;

    //tìm kiếm -----------------------
//    protected $tournament_name;
//    protected $participant_type_name;
//    protected $participant_group_name;
//    protected $participant_sub_group_name;
//    protected $parcitipant_class_name;
//    protected $athlete_type_name;
//    protected $is_check_false_name;
//    protected $athlete_birdthday;
//    protected $athlete_gender;
   protected $key_search;
//    protected $total_athlate;
    protected $column_end;

    public function __construct(
        $staffs,
//        $ar_tournamentCompetition,
//
//        //tìm kiếm -----------------------
//        $tournament_name,
//        $participant_type_name,
//        $participant_group_name,
//        $participant_sub_group_name,
//        $parcitipant_class_name,
//        $athlete_type_name,
//        $is_check_false_name,
//        $athlete_birdthday,
//        $athlete_gender,
       $key_search,
//        $total_athlate
)
    {
        $this->staffs = $staffs;
    //    $this->ar_tournamentCompetition = $ar_tournamentCompetition;

      //  $this->row_header = (10+count($this->staffs)+2);
        $this->row_header =6;

        //tìm kiếm -----------------------
//        $this->tournament_name = $tournament_name;
//        $this->participant_type_name = $participant_type_name;
//        $this->participant_group_name = $participant_group_name;
//        $this->participant_sub_group_name = $participant_sub_group_name;
//        $this->parcitipant_class_name = $parcitipant_class_name;
//        $this->athlete_type_name = $athlete_type_name;
//        $this->is_check_false_name = $is_check_false_name;
//        $this->athlete_birdthday = $athlete_birdthday;
//        $this->athlete_gender = $athlete_gender;
          $this->key_search = $key_search;
//        $this->total_athlate = $total_athlate;

        //Nội dung ở giữa : nếu có in ảnh thì đến hàng N, còn không in ảnh thì đến hàng M
        $column_end ="";
       // if(session('is_report_have_image') and  session('is_report_have_image')=="true") {
            $this->column_end = "H";
//        } else {
//            $this->column_end = "I";
//        }

    }
    public function view(): View
    {
       // dd($this->staffs);

        return view('export.staff.staff_export', [
            'staffs' => $this->staffs,
           // 'ar_tournamentCompetition' => $this->ar_tournamentCompetition,

           // 'tournament_id'=>session('search.tournament_id') ,

            'date_report'=>date("Y-m-d G:i:s"),

            //tìm kiếm -----------------------
//            'tournament_name' => $this->tournament_name,
//            'participant_type_name' => $this->participant_type_name,
//            'participant_group_name' => $this->participant_group_name,
//            'participant_sub_group_name' => $this->participant_sub_group_name,
//            'parcitipant_class_name' => $this->parcitipant_class_name,
//            'athlete_type_name' => $this->athlete_type_name,
//            'is_check_false_name' => $this->is_check_false_name,
//            'athlete_birdthday' => $this->athlete_birdthday,
//            'athlete_gender' => $this->athlete_gender,
          'key_search' => $this->key_search,
//            'total_athlate' => $this->total_athlate

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

      if(session('is_report_have_image') and  session('is_report_have_image')=="true") {

            $column_img = 'J';
            $row_img = ($this->row_header + 1);
            $imgs = [];
            foreach ($this->staffs as $k => $staff) {

                if (file_exists(str_replace("public/","",$staff['image_link']))) {

                    if (isset($staff['image_link']) and $staff['image_link'] != "") {
                        $path = str_replace('public/', '', $staff['image_link']);
                        $cell = $row_img + $k;

                        $drawing = new Drawing();
                        $drawing->setName($staff['name']);
                        $drawing->setPath(public_path($path));
                        $drawing->setWidth(130);
                        $drawing->setHeight(130);
                        $drawing->setOffsetY(1);
                        $drawing->setOffsetX(1);
                        $drawing->setCoordinates($column_img . $cell);
                        $imgs[] = $drawing;
                    }
                }
            }
            return $imgs;
        } else {
            return [

            ];
        }
    }

    //độ rộng các cột
    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 35, //Nội dung đăng ký thi đấu
            'C' => 30, //Họ và tên VĐV
            'D' => 20, //Ảnh chân dung
            'E' => 12, //Giới tính
            'F' => 18, //Ngày/tháng/năm sinh
            'G' => 18, //Mã VĐV
            'H' => 20, //Chiều cao (cm)
            'I' => 20, // Cân nặng (kg)
            'J' => 30, //Họ và tên phụ huynh

        ];
    }

    public function styles(Worksheet $sheet)
    {
        //hàng tiêu đề chữ đậm
        return [
            // Style the first row as bold text.
            $this->row_header   => [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        $rows = [];
        $cuong ="";

        return [

            AfterSheet::class    => function(AfterSheet $event)  {

                //font chữ toàn màn hình--------------
                $event->sheet->getStyle('A1:'.$this->column_end.($this->row_header+count($this->staffs)))->applyFromArray([

                    'font' => array(
                        'name'      =>  'Times New Roman',
                        'size'      =>  12,
                        //  'bold'      =>  true
                    )

                ]);

                //tiêu đề-------------
                $event->sheet->getDelegate()->getRowDimension($this->row_header)->setRowHeight(30);
               // dd($this->row_header);

                //border tiêu đề-----------------
                $event->sheet->getStyle('A'.$this->row_header.':'.$this->column_end.$this->row_header)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ])->getAlignment()->setWrapText(true);

                //nôi dung-----------------------------
                for($i = 0; $i< count($this->staffs); $i ++ ){

                    //từ A13 -U13 :.....

                    //chiêu cao hàng
                    $event->sheet->getDelegate()->getRowDimension(($i+$this->row_header+1))->setRowHeight(30);

                    //border + color
                    $event->sheet->getStyle('A'.($i+$this->row_header+1).':'.$this->column_end.($i+$this->row_header+1))->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                        'font' => array(
                            'name'      =>  'Times New Roman',
                            'size'      =>  12,
                          //  'bold'      =>  true
                        )

                    ])->getAlignment()->setWrapText(true);
                }

            },
        ];
    }
}
