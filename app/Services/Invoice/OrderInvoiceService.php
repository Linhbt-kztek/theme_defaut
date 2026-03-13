<?php

namespace App\Services\Invoice;



use App\Models\Invoice;
use App\Models\Order;
use App\Repositories\Invoice\InvoiceRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\PaymentStatus\PaymentStatusRepository;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Services\ReturnResultService;
use Exception;
use App\Services\Invoice\ApiService as InvoiceApiService;
use App\Services\Invoice\TicketService as InvoiceTicketService;
use App\Services\ConfigService;
use App\Services\LogService;
use App\Services\Invoice\include\BaseService;

class OrderInvoiceService
{
    private $provider;
    private $bill_taxRate;
    private $bill_templateCode;
    private $bill_invoiceTypeCode;
    private $bill_symbolCode;
    private $ticket_taxRate;


    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        // protected TicketTypeRepository     $ticketTypeRepository,
        protected UserRepository $userRepository,
        protected PaymentStatusRepository $paymentStatusRepository,
        // protected TicketRepository         $ticketRepository,
        protected ReturnResultService $returnResultServiceRepo,
        protected InvoiceApiService $invoiceApiServiceRepo,
        protected InvoiceRepositoryInterface $invoiceRepo,
        protected LogService $logService,
        protected InvoiceTicketService $invoiceTicketService,
        protected ConfigService $configService,
        protected BaseService $baseService
    ) {
        //Nhà cung cấp dịch vụ là  MISA
        $config = $this->configService->getConfig();
        $invoiceConfiguration = $config["invoiceConfiguration"] ?? [];

        // $this->provider = $invoiceConfiguration["provider"] ?? "";
        $this->provider = "Bizzi";


        //---invoice -----
        $billConfig = $invoiceConfiguration["bill"] ?? [];
        $this->bill_taxRate = $billConfig["taxRate"] ?? null;
        $this->bill_templateCode = $billConfig["templateCode"] ?? null;
        $this->bill_invoiceTypeCode = $billConfig["invoiceTypeCode"] ?? null;
        $this->bill_symbolCode = $billConfig["symbolCode"] ?? null;

        $ticketConfig = $invoiceConfiguration["ticket"] ?? [];
        $this->ticket_taxRate = $ticketConfig["taxRate"] ?? null;
        //---invoice -----

        $this->companyName = $config["companyName"] ?? null;
        $this->taxCode = $config["taxCode"] ?? null;
    }


    /**
     *  phân loại để gọi service tương ứng : là vé điện tử hay hoá đơn điện tử
     * @param $order_id
     * @return array
     */
    public function create_invoice($order_id)
    {
        $order = $this->orderRepository->getById($order_id);

        if ($order) {
            $invoice = $this->create_order_invoice($order_id);

            $type_display_error = 1;

            // if (isset($order->customer->type) && ((int) $order->customer->type == 1 || (int) $order->customer->type == 2)) {
            // Nếu type là 1 hoặc 2, gọi create_order_invoice
            // $invoice = $this->create_order_invoice($order_id);
            // $type_display_error = 1;

            // } 
            // else {
            //     // Các trường hợp còn lại, gọi create_ticket_invoice
            //     $invoice = $this->create_ticket_invoice($order);

            //     $type_display_error = 2;
            // }
            if ($invoice["status"] == 90) {
                $respone = $this->returnResultServiceRepo->return_error_midle($invoice["message"], 90, $invoice["data"], $type_display_error);
            } else {
                $respone = $this->returnResultServiceRepo->return_success_midle($invoice["data"], $invoice["message"]); //tra ve : tong so ve dt thanh cong / tong so ve dt cua order
            }

        } else {
            $respone = $this->returnResultServiceRepo->return_error_midle("Không tìm thấy order_id=" . $order_id);
        }

        return $respone;
    }

    /**
     * tạo 1 hoá đơn điện tử cho 1 hoá đơn order_id là đại lý , kho , là hàm con của hàm  create_invoice
     * @param $order_id
     * @return array
     */
    private function create_order_invoice($order_id)
    {
        $this->logService->debug(["order_id=" . $order_id], __FUNCTION__);

        //lưu thừi gian vanh gọi đến
        $first_time = Carbon::now();


        //bắt lần trước đã tạo nhưng lỗi:
        $is_create_before_error = 0;
        $bill_id_before = "";
        //----------------------------


        //1.kiẻm tra trong bảng bill , cột order_id :  xem status , đã có vé điẹn tử chưa =>  thì trả về luôn
        $check_exit_order = $this->check_exit_bill_for_order($order_id, $bill_id_before, $is_create_before_error);


        if ($check_exit_order["status"] == 90) {
            return $this->returnResultServiceRepo->return_error_midle($check_exit_order["message"]);
        } else {
            //đã có hoá dơn tạo thành công rồi =>tra về kq luôn
            if ($check_exit_order["data"]["return"] == 1) {
                return $this->returnResultServiceRepo->return_success_midle($check_exit_order["message"]);
            }
        }
        $order = null;

        DB::beginTransaction();
        try {

            //2.kiẻm tra bảng order : có tồn tại order_id không ,  và có đang tạo bill ở luống khác không =>OK thì trả  về thông tin order -----------
            $check_validate = $this->validate_create_invoice($order_id);

            if ($check_validate["status"] != 200) {
                throw new Exception($check_validate["message"]);
            }
            $order = $check_validate["data"];
            // $tickets = $order->tickets;




            //3.update trạng thái order đang tạo hddt-------------------
            $is_check = $this->update_status_create_invoice(1, $order_id);

            if ($is_check["status"] == 90) {
                throw new Exception($is_check["error"]);
            }
            //---------------------------------------------------


            //4. đặt mặc định:  gọi api kiểm tra trạng thái vé/Hđdt và trả về  false :  là đã tạo trước đó và tạo chưa thành công
            $invoice_create_before_true_after_get_status = false;



            //5. nếu trước kia  đã tạo Hddt rồi : gọi api kiểm tra trạng thái Hddt dt
            if ($is_create_before_error == 1) {
                //gọi api kiểm tra trạng thái
                $get_status_invoice = $this->get_invoice($order_id);
                

                if ($get_status_invoice["status"] == 200) {
                    //gọi api kiểm tra trạng thái vé/Hđdt và đã tạo trước đó thành công
                    $invoice_create_before_true_after_get_status = true;
                }

            }



            //6.nếu trước đó chưa có hoặc thất bại => gọi api tạo HD điện tử ----------
            if (!$invoice_create_before_true_after_get_status) {
                $rs_call_invoice_service = $this->call_invoice_service($order);
                // nếu bên api trả về lỗi , vẫn giữ lại để ghi log vào bảng bill nhé
                $rs_invoiceService = $rs_call_invoice_service["data"];
            }

            //7.tạo dữ liêu  cho bảng bill-----------
            $bill_id = getGUID();
            $bill = [];
            $bill["id"] = $bill_id;
            $bill["order_id"] = $order_id;
            $bill["ticket_id"] = "";
            $bill["type"] = 0; //HDdt
            $bill["first_time"] = $first_time;
            $bill["last_time"] = Carbon::now();
            //---------------------------------------------

            //xử lý kết quả  tạo vé/hddt
            $is_create_invoice_true = 1;
            $error_create_invoice = "";

            //nếu check api trạng thái : trước đó nó đã tạo hddt thành công
            if ($invoice_create_before_true_after_get_status) {
                //lưu lại để ghi log đã trả gì về : là json
                $response_data = $get_status_invoice['response_data'];
                $bill["response_data"] = $response_data;

                $result = $get_status_invoice['result'];
                //số hoá đơn
                $bill["number_of_bill"] = $result["lookupInformation"]["invoiceNumber"];
                $bill["code_of_bill"] = ""; //mã hd của Dương trả về bỏ rồi
                $bill["status"] = 1; //tạo hoá đơn thành công
                $bill["seri_code"] = Str::after($result["lookupInformation"]["invoiceNumber"], $result["lookupInformation"]["reservationCode"]);
                ;
                $bill["reservation_code"] = $result["lookupInformation"]["reservationCode"];


            } else {
                //đã gọi lại api tạo hd đt:

                if ($rs_call_invoice_service["status"] == 200) {

                    //tạo hd thành công
                    if ($rs_invoiceService['status'] == 200) {

                        //lưu lại để ghi log đã trả gì về : là json
                        $response_data = $rs_invoiceService['response_data'];
                        $bill["response_data"] = $response_data;

                        $result = $rs_invoiceService['result'];
                        //số hoá đơn
                        $bill["number_of_bill"] = $result["lookupInformation"]["invoiceNumber"];
                        $bill["code_of_bill"] = ""; //mã hd của Dương trả về bỏ rồi
                        $bill["status"] = 1; //tạo hoá đơn thành công
                        $bill["seri_code"] = Str::after($result["lookupInformation"]["invoiceNumber"], $result["invoiceConfiguration"]["reservationCode"]);
                        ;
                        $bill["reservation_code"] = $result["invoiceConfiguration"]["reservationCode"];

                    } else {

                        //la tạo hoá đơn thất bại do api trả về

                        $is_create_invoice_true = 0;
                        $error_create_invoice = $rs_invoiceService['message'] ?? "";
                        $bill["response_data"] = $rs_invoiceService["message"] ?? "";
                        //số hoá đơn
                        $bill["number_of_bill"] = ""; //lỗi nên rỗng
                        $bill["code_of_bill"] = ""; //lỗi nên rỗng
                        $bill["status"] = 2; //tạo hoá đơn thất bại

                    }

                } else {
                    //loi trả về từ catch exception của hàm call_invoice_service

                    $is_create_invoice_true = 0;
                    $error_create_invoice = $rs_call_invoice_service['message'] ?? "";
                    $bill["response_data"] = $rs_call_invoice_service["message"] ?? "";
                    //số hoá đơn
                    $bill["number_of_bill"] = ""; //lỗi nên rỗng
                    $bill["code_of_bill"] = ""; //lỗi nên rỗng
                    $bill["status"] = 2; //tạo hoá đơn thất bại

                }

            }
            //---------------------------------------------

            //8.lần trước đã tạo hddt  rồi nhưng lỗi => xoá cũ đi  hoá dơn cũ tạo lỗi
            if ($is_create_before_error == 1) {
                //xoá đi lần cân này
                if (
                    $this->invoiceRepo->update($bill_id_before, [
                        'is_delete' => 1,
                        'deleted_at' => Carbon::now()
                    ]) === false
                ) {
                    throw new \Exception("Lỗi: xoá bảng weighing_bill !");
                }
                ;
            }


            //9.tạo mới bill ------------
            $rs_create_bill = $this->create_bill($bill);


            if ($rs_create_bill["status"] == 90) {
                throw new \Exception("Lỗi: update bảng  bill !");
            }

            //10.update trạng thái làm việc xong----
            $is_check = $this->update_status_create_invoice(0, $order_id);
            if ($is_check["status"] == 90) {
                throw new Exception($is_check["error"]);
            }

            DB::commit();

            //11. trả về kết quả----
            if ($is_create_invoice_true == 1)
                $respone = $this->returnResultServiceRepo->return_success_midle();
            else
                $respone = $this->returnResultServiceRepo->return_error_midle($error_create_invoice);
            //---------------------------------------------


        } catch (\Throwable $exception) {

            DB::rollBack();
            $this->logService->debug([$exception->getMessage(), "order_id=$order_id"], __FUNCTION__);

            $respone = $this->returnResultServiceRepo->return_error_midle($exception->getMessage());
        }

        if ((int) $respone["status"] == 90) {
            //chuyển lỗi về cùng 1 format ,  để dùng chung giữa hddt và vé đt
            $respone = $this->baseService->processResponseError($respone, $order, 'order');
        }
        return $respone;
    }

    /**
     * kiẻm tra trong bảng bill , cột order_id :  xem status , đã có hd điẹn tử chưa =>  thì trả về luôn
     * @param $order_id
     * @param $bill_id_before
     * @param $is_create_before_error
     * @return array
     */
    private function check_exit_bill_for_order($order_id, &$bill_id_before, &$is_create_before_error)
    {
        if (empty($order_id)) {
            $error = "Lỗi : dữ liệu gửi lên thiếu giá trị order_id ";
            return $this->returnResultServiceRepo->return_error_midle($error);
        } else if (strlen($order_id) > 55) {
            $error = "Độ dài của Order_id max là 55";
            return $this->returnResultServiceRepo->return_error_midle($error);
        }

        $respone = [];
        try {
            //kiểm tra $order_id  đã tạo hoá đơn ròi thì ko  tạo nữa mà tra về kết quả luôn
            $check_bill = $this->invoiceRepo->getWithFilter("", 0, -1, [], [
                ["order_id", $order_id]
            ]);



            if ((int) $check_bill->count() == 1) {
                if ((int) $check_bill[0]->status == 1) {

                    //hoá đơn đã tạo thành công => trả về kết quả dã có thôi
                    return $this->returnResultServiceRepo->return_success_midle([
                        "return" => 1
                    ], "Đã có hoá đơn điện tử của hoá đơn này");


                } else if ((int) $check_bill[0]->status == 2) {

                    //hoá đơn đã bị tạo lỗi từ lần trước -> tí nữa phải xoá đi và update nhé
                    $bill_id_before = $check_bill[0]->id;
                    $is_create_before_error = 1;

                    return $this->returnResultServiceRepo->return_success_midle([
                        "return" => 0
                    ], "hoá đơn đã bị tạo lỗi từ lần trước");

                } else {
                    return $this->returnResultServiceRepo->return_error_midle("Lỗi: bill.status=" . $check_bill[0]->status . " chưa định nghĩa");
                }

            } else if ($check_bill->count() > 1) {
                return $this->returnResultServiceRepo->return_error_midle("Lỗi: tạo nhiều lần hoá đơn cho order_id=" . $order_id);
            }

        } catch (\Throwable $ex) {
            return $this->returnResultServiceRepo->return_error_midle($ex->getMessage());
        }

        return $this->returnResultServiceRepo->return_success_midle(["return" => 0]);
    }

    /**
     *
     * @param $order_id
     * @return array : giá trị order của order_id
     */
    private function validate_create_invoice($order_id)
    {

        if (empty($order_id)) {
            $error = "Lỗi : dữ liệu gửi lên thiếu giá trị order_id ";
            return $this->returnResultServiceRepo->return_error_midle($error);
        }
        //kiểm tra xem hoá đơn này có đang trong quá trình tạo dở hay không -
        $order = $this->orderRepository->getById($order_id);

        if ($order === null) {
            $error = "Không tồn tại lần cân order_id=" . $order_id;
            return $this->returnResultServiceRepo->return_error_midle($error);
        }

        if ($order->status_bill_id == 1) {
            $error = "Lần hoá đơn này đang trong quá trình tạo hoá đơn điện tử request khác !";
            //chú ý mã 91
            return $this->returnResultServiceRepo->return_error_midle($error, 91);
        }

        return $this->returnResultServiceRepo->return_success_midle($order);
    }

    //$status : 1 là đang làm việc , 0 là không làm việc
    private function update_status_create_invoice($status, $order_id)
    {

        try {
            if (
                $this->orderRepository->update($order_id, [
                    'status_bill_id' => $status,
                    'updated_at' => Carbon::now()
                ]) === false
            ) {

                throw new \Exception("Lỗi: update bảng order !");
            }
            ;

            $respone = $this->returnResultServiceRepo->return_success_midle();

        } catch (\Throwable $exception) {

            $this->logService->debug([$exception->getMessage()], __FUNCTION__);
            $respone = $this->returnResultServiceRepo->return_error_midle($exception->getMessage());
        }

        return $respone;
    }

    //hàm tạo hoá đơn điện tử trung gian trong hàm create_invoice
    private function call_invoice_service($order)
    {

        try {

            $config = $this->configService->getConfig();
            $invoiceConfiguration = $config["invoiceConfiguration"] ?? [];
            $user = auth()->user();


            // $company = [
            //     "Name" => $config["companyName"] ?? null,
            //     "taxCode" => $config["taxCode"] ?? null
            // ];

            $company = [
                "Name" => $config["companyName"] ?? null,
                "taxCode" => 'Bizzi',
            ];


            if (empty($company['Name']) || empty($company['taxCode'])) {
                throw new Exception('Tên công ty và mã số thuế là bắt buộc.');
            }

            //paymentItems------------------
            $paymentItems = $this->setPaymentItems($order, false);


            //creator------------------
            $creator = [
                "id" => Auth::user()->id,
                "Name" => !empty(Auth::user()->name) ? Auth::user()->name : "no_name"
            ];



            //buyer------------------

            //additionalData------------------
            $additionalData = [];

            $invoiceConfiguration_type = null;

            //logic : giống như ở table của menu hoá đơn:  khi phân biệt vé hay hddt
            if (!empty($order->customer_classification_id)) {
                //nếu mua vé tại quầy
                if ((int) $order->customer->type == 1 || (int) $order->customer->type == 2) {
                    // đối tượng mua là : đại lý , nhà xe
                    $invoiceConfiguration_type = 0;

                } else {
                    // đối tượng mua : khách lẻ
                    $invoiceConfiguration_type = 1;
                }
            } else {
                // truong họp con lai :là khách lẻ mua online
                $invoiceConfiguration_type = 1;
            }

            $buyer = null;

            // if ($invoiceConfiguration_type == Invoice::TYPE_BILL) {
            //     // $buyer = [
            //     //     "Name" => $order->customer->name,
            //     //     "Code" => $order->customer->code,
            //     //     "TaxCode" => $order->customer->taxCode,
            //     //     "Phonenumber" => $order->customer->phone ?? "",
            //     //     "Email" => $order->customer->email ?? "",
            //     //     "Address" => $order->customer->address,
            //     // ];

            //     $buyer = [
            //         "Name" => $order->customer->name,
            //         "Code" => $order->customer->code,
            //         "TaxCode" => null,
            //         "Phonenumber" => $order->customer->phone ?? "",
            //         "Email" => $order->customer->email ?? "",
            //         "Address" => $order->customer->address,
            //     ];


            //     $type = "bill";

            // } else if ($invoiceConfiguration_type == Invoice::TYPE_TICKET) {
            //     //là vé dt thi ko co buyer
            //     $type = "ticket";
            // }

            $invoiceConfiguration = [
                "templateCode" => $this->bill_templateCode, //lay trong cau hinh
                "invoiceTypeCode" => $this->bill_invoiceTypeCode, //lay trong cau hinh
                "symbolCode" => $this->bill_symbolCode, //lay trong cau hinh
                "Type" => $invoiceConfiguration_type //van lấy ở trên
            ];

            //tạo object invoice
            $send = $this->invoiceApiServiceRepo->CreatObjectInvoice($order->id, $paymentItems, $creator, $buyer, $additionalData, $invoiceConfiguration_type, $this->provider, $company, $invoiceConfiguration, $this->bill_taxRate);


            //tk login hddt
            $user_login = [
                "username" => config("invoice.api_login.param.username"),
                "password" => config("invoice.api_login.param.password"),
            ];


            $rs_invoiceService = $this->invoiceApiServiceRepo->create_invoice($user_login, $send, $this->provider);

            $respone = $this->returnResultServiceRepo->return_success_midle($rs_invoiceService);


        } catch (\Throwable $ex) {

            DB::rollBack();
            $this->logService->debug([$ex->getMessage()], __FUNCTION__);
            $respone = $this->returnResultServiceRepo->return_error_midle($ex->getMessage());

        }
        return $respone;


    }

    // hàm tạo paymentItems gửi api service 
    private function setPaymentItems($order, $has_ticket = false)
    {
        if ($has_ticket) {
            if ($order->tickets->count() > 0) {
                foreach ($order->tickets as $key => $item) {
                    $paymentItems[] = [
                        "Name" => $item["ticket_type_name"],
                        "Code" => $item["code"],
                        "Description" => $item["ticket_type_name"] . "-",
                        "UnitName" => config('invoice.paymentItems.UnitName'),//lay trong cau hinh
                        "Category" => $item["ticket_type_name"],
                        "Quantity" => config('invoice.paymentItems.Quantity'), //lay trong cau hinh
                        "UnitPrice" => $item["price"],
                        "TaxRate" => $this->bill_taxRate
                    ];
                }
            } else {
                throw new Exception(" order_id=" . $order->id . " không có vé mua !");
            }
        } else {
            if ($order['type'] == 1)
                $orderName = "Sân lẻ";
            if ($order['type'] == 2)
                $orderName = "Sân cố định";
            if ($order['type'] == 1)
                $orderName = "Tham gia sân ghép";

            $paymentItems[] = [
                "Name" => $orderName,
                "Code" => $order["code"],
                "Description" => null,
                "UnitName" => config('invoice.paymentItems.Session'),//lay trong cau hinh
                "Category" => null,
                "Quantity" => config('invoice.paymentItems.Quantity'), //lay trong cau hinh
                "UnitPrice" => $order["amount"],
                "TaxRate" => $this->bill_taxRate
            ];
        }

        return $paymentItems;
    }

    /*
     *  gọi lại hoá đơn
     */
    private function get_invoice($order_id)
    {

        //tk login hddt
        $user_login = [
            "username" => config("invoice.api_login.param.username"),
            "password" => config("invoice.api_login.param.password"),
        ];


        $result = $this->invoiceApiServiceRepo->check_status_order($user_login, $order_id, $this->provider);


        return $result;
    }

    private function preview($order_id)
    {
        $base64Pdf = "QyNjINCiUlRU9GDQo = ";

        return view('admin/order/include/preview', ['base64Pdf' => $base64Pdf]);

    }

    //thêm voà bảng weighing_bill
    public function create_bill($data)
    {

        $data = [
            "id" => getGUID(),
            "order_id" => $data["order_id"],
            "number_of_bill" => $data["number_of_bill"],
            "code_of_bill" => $data["code_of_bill"],
            "ticket_id" => "", //tao hd nên ko có ticket_id

            "first_time" => $data["first_time"],
            "last_time" => $data["last_time"],
            "response_data" => $data["response_data"],
            "status" => $data["status"],
            "type" => $data["type"],

            "is_delete" => 0,
            "created_at" => Carbon::now(),

        ];
        DB::beginTransaction();
        try {
            if ($this->invoiceRepo->create($data) === false) {
                throw new \Exception("Lỗi insert bảng weighing_bill");
            }

            DB::commit();
            $respone = $this->returnResultServiceRepo->return_success_midle();

        } catch (\Throwable $ex) {
            DB::rollBack();
            $this->logService->debug([$ex->getMessage()], __FUNCTION__ . __LINE__ . __FILE__);
            $respone = $this->returnResultServiceRepo->return_error_midle($ex->getMessage());

        }
        return $respone;

    }


    // /**
    //  *  tạo các vé điện tử cho 1 order_id là khách lẻ , là hàm con của hàm  create_invoice
    //  * @param $order_id
    //  * @return void
    //  */
    // private function sent_ticket_invoice($ticket)
    // {
    //     $rs = $this->invoiceTicketService->create_invoice($ticket->id);

    //     // Kiểm tra trạng thái trả về
    //     if ((int) $rs["status"] == 200) {
    //         // $results[] = [
    //         //     'ticket_id' => $ticket->id,
    //         //     'ticket_code' => $ticket->code,
    //         //     'status' => 'tạo vé điện tử thành công',
    //         //     'error_code' => "" // Không có lỗi
    //         // ];
    //         // $data["total_success"]++;
    //         return [
    //             'ticket_id' => $ticket->id,
    //             'ticket_code' => $ticket->code,
    //             'status' => 'tạo vé điện tử thành công',
    //             'error_code' => "" // Không có lỗi
    //         ];
    //     }
    //     else {
    //         // $results[] = $rs["data"][0];
    //         // $data["total_false"]++;
    //         return $this->sent_ticket_invoice($ticket);
    //     }
    // }

    // private function create_ticket_invoice($order)
    // {
    //     $data = [
    //         "total_success" => 0,
    //         "total_false" => 0,
    //     ];

    //     // Mảng để lưu tất cả kết quả
    //     $results = [];
    //     if (!empty($order->tickets)) {
    //         foreach ($order->tickets as $k => $ticket) {
    //             $results[] = $this->sent_ticket_invoice($ticket);
    //             $data["total_success"]++;

    //             // Gọi function tạo invoice
    //             // $rs = $this->invoiceTicketService->create_invoice($ticket->id);

    //             // // Kiểm tra trạng thái trả về
    //             // if ((int)$rs["status"] == 200) {
    //             //     $results[] = [
    //             //         'ticket_id' => $ticket->id,
    //             //         'ticket_code' => $ticket->code,
    //             //         'status' => 'tạo vé điện tử thành công',
    //             //         'error_code' => "" // Không có lỗi
    //             //     ];
    //             //     $data["total_success"]++;
    //             // } else {
    //             //     $results[] = $rs["data"][0];
    //             //     $data["total_false"]++;
    //             // }
    //         }
    //     }
    //     // dd($results);
    //     $message = "Tạo thành công " . $data["total_success"] . "/" . $order->tickets->count() . " vé ĐT.";

    //     if ((int)$data["total_success"] == (int)$order->tickets->count()) {
    //         //update order.bill_success =1 là đã tạo thành công vé điện tử cho mọi vé của hoá đơn
    //         $rs = $this->orderRepository->update($order->id, [
    //             "bill_success" => 1
    //         ]);
    //         if ($rs)
    //             return $this->returnResultServiceRepo->return_success_midle($data, $message);
    //         else
    //             return $this->returnResultServiceRepo->return_error_midle("update bảng order lỗi !");
    //     } else {

    //         //update order.bill_success =0 là chưa tạo thành công vé điện tử cho mọi vé của hoá đơn
    //         $rs = $this->orderRepository->update($order->id, [
    //             "bill_success" => 0
    //         ]);
    //         if ($rs)
    //             return $this->returnResultServiceRepo->return_error_midle($message, 90, $results);
    //         else
    //             return $this->returnResultServiceRepo->return_error_midle("update bảng order lỗi !");
    //     }

    // }
}
