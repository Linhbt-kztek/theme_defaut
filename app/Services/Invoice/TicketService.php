<?php

namespace App\Services\Invoice;

use App\Exports\Order\ReportOrderExport;
use App\Exports\Ticket\ReportTicketExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Invoice\InvoiceRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\PaymentStatus\PaymentStatusRepository;
use App\Repositories\Ticket\TicketRepository;
use App\Repositories\TicketType\TicketTypeRepository;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

//use Mockery\Exception;
use Nette\Utils\Random;
use App\Services\ReturnResultService;
use Exception;
use App\Repositories\Bill\BillRepositoryInterface;

use App\Services\LogService;
use App\Services\Invoice\ApiService as InvoiApiService;
use App\Services\Invoice\include\BaseService;
use App\Services\ConfigService;
use App\Models\Bill;
use App\Models\Config as ModelsConfig;

class TicketService
{
    private $provider;
    private $bill_taxRate;
    private $bill_templateCode;
    private $bill_invoiceTypeCode;
    private $bill_symbolCode;
    private $ticket_taxRate;

    private $companyName;
    private $use_receipt;
    private $taxCode;

    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        // protected TicketTypeRepository $ticketTypeRepository,
        protected UserRepository $userRepository,
        protected PaymentStatusRepository $paymentStatusRepository,
        // protected TicketRepository $ticketRepository,
        protected ReturnResultService $returnResultServiceRepo,
        protected InvoiApiService $invoiceApiServiceRepo,
        protected InvoiceRepositoryInterface $billRepo,
        protected LogService $logService,
        protected ConfigService $configService,
        protected BaseService $baseService
    ) {
        //Nhà cung cấp dịch vụ là  MISA
        $config = $this->configService->getConfig();
        $invoiceConfiguration = $config["invoiceConfiguration"] ?? [];

        $this->provider = $invoiceConfiguration["provider"] ?? "";

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
        $this->use_receipt = $config["use_receipt"] ?? 0;
    }

    /**
     *  tạo vé điện tử cho 1 vé
     * @param $ticket_id
     * @return array
     */
    public function create_invoice($ticket_id)
    {
        $this->logService->debug(["ticket_id=" . $ticket_id]);

        //lưu thừi gian vanh gọi đến
        $first_time = Carbon::now();

        //bắt lần trước đã tạo nhưng lỗi:
        $is_create_before_error = 0;
        $bill_id_before = "";
        //----------------------------
        //kiẻm tra trong bảng bill , cột ticket_id :  xem status , đã có vé điẹn tử chưa =>  thì trả về luôn
        $check_exit_ticket = $this->check_exit_bill_for_ticket($ticket_id, $bill_id_before, $is_create_before_error);

        if ($check_exit_ticket["status"] == 90) {
            return $this->returnResultServiceRepo->return_error_midle($check_exit_ticket["message"]);
        } else {
            //đã có hoá dơn tạo thành công rồi =>tra về kq luôn
            if ($check_exit_ticket["data"]["return"] == 1) {
                return $this->returnResultServiceRepo->return_success_midle($check_exit_ticket["message"]);
            }
        }
        
        DB::beginTransaction();
        try {

            //1.kiẻm tra bảng ticket : có tồn tại ticket_id ,  và có đang tạo bill ở luống khác không => Ok thì trả  về thông tin ticket -----------
            $check_validate = $this->validate_create_invoice($ticket_id);
            
         
            if ($check_validate["status"] != 200) {
                throw new Exception($check_validate["message"]);
            }
            $ticket = $check_validate["data"];

            //2.update trạng thái ticket đang tạo vé dt-------------------
            $is_check = $this->update_ticket_status_create_invoice(1, $ticket_id);

            if ($is_check["status"] == 90) {
                throw new Exception($is_check["error"]);
            }
            //---------------------------------------------------

            //vé dt gọi api kiểm tra trạng thái : false là  thất bại
            $get_status_ticket_invoice_from_api = false;

            //nếu trước kia  đã tạo vé dt rồi : gọi api kiểm tra trạng thái vé dt
            if ($is_create_before_error == 1) {
                // gọi api kiểm tra trạng thái
                $get_status_invoice = $this->get_invoice($ticket_id);
                

                if ($get_status_invoice["status"] == 200) {
                    $get_status_ticket_invoice_from_api = true;
                }

            }


            //4.nếu trước đó chưa có hoặc thất bại => gọi api tạo vé điện tử ----------
            if (!$get_status_ticket_invoice_from_api) {
                $rs_call_invoice_service = $this->call_invoice_service($ticket);
                //giữ nguyên lỗi để ghi vào bảng bill
              
                $rs_invoiceService = $rs_call_invoice_service["data"];
            }
            
            //tạo dữ liêu ban đầu cho bảng bill-----------
            $bill_id = getGUID();
            $bill = [];
            $bill["id"] = $bill_id;
            $bill["order_id"] = $ticket->order_id;
            $bill["ticket_id"] = $ticket_id;
            $bill["type"] = 1; //vé dt
            $bill["first_time"] = $first_time;
            $bill["last_time"] = Carbon::now();
            //---------------------------------------------

            //5.xử lý kết quả của server tạo vé dt
            $is_create_invoice_true = 1;
            $error_create_invoice = "";

            //nếu check api trạng thái : trước đó nó đã tạo vé dt thành công
            if ($get_status_ticket_invoice_from_api) {

                //lưu lại để ghi log đã trả gì về : là json
                $response_data = $get_status_invoice['response_data'];
                $bill["response_data"] = $response_data;

                $result = $get_status_invoice['result'];
                //số hoá đơn
                $bill["number_of_bill"] = $result["lookupInformation"]["invoiceNumber"];
            
                $bill["code_of_bill"] = ""; //mã hd của Dương trả về bỏ rồi
                $bill["status"] = 1; //tạo hoá đơn thành công

                $bill['reservation_code'] = $result["lookupInformation"]["reservationCode"]??null;
                $bill['seri_code'] = Str::after($result["lookupInformation"]["invoiceNumber"], $result["invoiceConfiguration"]["templateCode"])??null; 
                $bill['symbolCode'] = $result["invoiceConfiguration"]["symbolCode"]??null; 

            } else {
                //đã gọi lại api tạo vé đt:

                if ($rs_call_invoice_service["status"] == 200) {

                    //tạo vé thành công
                    if ($rs_invoiceService['status'] == 200) {

                        //lưu lại để ghi log đã trả gì về : là json
                        $response_data = $rs_invoiceService['response_data'];
                        $bill["response_data"] = $response_data;

                        $result = $rs_invoiceService['result'];
                        //số hoá đơn
                        $bill["number_of_bill"] = $result["lookupInformation"]["invoiceNumber"];
                        $bill["code_of_bill"] = ""; //mã hd của Dương trả về bỏ rồi
                        $bill["status"] = 1; //tạo hoá đơn thành công

                        $bill['reservation_code'] = $result["lookupInformation"]["reservationCode"]??null;
                        $bill['seri_code'] = Str::after($result["lookupInformation"]["invoiceNumber"], $result["invoiceConfiguration"]["templateCode"])??null; 
                        $bill['symbolCode'] = $result["invoiceConfiguration"]["symbolCode"]??null; 


                    } else {

                        //la tạo vé thất bại
                        $is_create_invoice_true = 0;
                        $error_create_invoice = $rs_invoiceService['message'];
                        $bill["response_data"] = $rs_invoiceService["message"];
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
            
            //7.lần trước đã tạo vé  rồi nhưng lỗi => xoá cũ đi  hoá dơn cũ tạo lỗi
            if ($is_create_before_error == 1) {
                //xoá đi lần cân này
                if (
                    $this->billRepo->update($bill_id_before, [
                        'is_delete' => 1,
                        'deleted_at' => Carbon::now()
                    ]) === false
                ) {
                    throw new \Exception("Lỗi: xoá bảng weighing_bill !");
                }
                ;
            }

            //8.tạo mới bill ------------
            $rs_create_bill = $this->create_bill($bill);
            if ($rs_create_bill["status"] == 90) {
                throw new \Exception("Lỗi: update bảng  bill !");
            }

            //9.update trạng thái làm việc xong----
            $is_check = $this->update_ticket_status_create_invoice(0, $ticket_id);
            if ($is_check["status"] == 90) {
                throw new Exception($is_check["error"]);
            }

            DB::commit();

            //trả về ----
            // tạo hoá đơn thành công
            if ($is_create_invoice_true == 1) {

                $respone = $this->returnResultServiceRepo->return_success_midle();

                //kiểm tra và update cột order.bill_success (đã tạo thành công mọi vé đt của hd này)
                $check_bill_success = $this->update_bill_success($ticket->order_id);
                if (!$check_bill_success) {
                    $respone = $this->returnResultServiceRepo->return_error_midle("Lỗi update  order.bill_success !");
                }


            } else
                $respone = $this->returnResultServiceRepo->return_error_midle($error_create_invoice);
            //---------------------------------------------


        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->logService->debug([$exception->getMessage(), "ticket_id=$ticket_id"], __FUNCTION__);

            $respone = $this->returnResultServiceRepo->return_error_midle($exception->getMessage());
        }

        if ((int) $respone["status"] == 90) {
            //chuyển lỗi về cùng 1 format ,  để dùng chung giữa hddt và vé đt
            $respone = $this->baseService->processResponseError($respone, $ticket, 'ticket');
        }
        return $respone;
    }

    /**
     * kiểm tra đơn hàng này đã tạo đủ vé dt thành công chưa thì update cột  bill_success =1
     * @param $order_id
     * @return bool
     */
    private function update_bill_success($order_id)
    {
        $tickets = $this->ticketRepository->getData(["order_id" => $order_id]);
        $bill = $this->billRepo->getData(["order_id" => $order_id, "type" => 1]);

        if ($tickets->count() == $bill->count() and $bill->count() > 0) {

            return $this->orderRepository->update($order_id, [
                "bill_success" => 1
            ]);

        }
        return true;


    }

    /**
     * kiẻm tra trong bảng bill , cột ticket_id :  xem status , đã có vé điẹn tử chưa =>  thì trả về luôn
     * @param $ticket_id
     * @param $bill_id_before
     * @param $is_create_before_error
     * @return array
     */
    private function check_exit_bill_for_ticket($ticket_id, &$bill_id_before, &$is_create_before_error)
    {

        if (empty($ticket_id)) {
            $error = "Lỗi : dữ liệu gửi lên thiếu giá trị ticket_id ";
            return $this->returnResultServiceRepo->return_error_midle($error);
        } else if (strlen($ticket_id) > 55) {
            $error = "Độ dài của ticket_id max là 55";
            return $this->returnResultServiceRepo->return_error_midle($error);
        }

        $respone = [];
        try {
            //kiểm tra $ticket_id  đã tạo vé đt ròi thì ko  tạo nữa mà tra về kết quả luôn
            $check_bill = $this->billRepo->getWithFilter("", 0, -1, [], [
                ["ticket_id", $ticket_id]
            ]);

            if ((int) $check_bill->count() == 1) {

                //đã tạo thành công
                if ((int) $check_bill[0]->status == 1) {

                    //hoá đơn đã tạo thành công => trả về kết quả dã có thôi
                    return $this->returnResultServiceRepo->return_success_midle([
                        "return" => 1
                    ], "Đã có vé điện tử của vé này");


                } else if ((int) $check_bill[0]->status == 2) {

                    //vé dt đã bị tạo lỗi từ lần trước -> tí nữa phải xoá đi và update nhé
                    $bill_id_before = $check_bill[0]->id;
                    $is_create_before_error = 1;

                    return $this->returnResultServiceRepo->return_success_midle([
                        "return" => 0
                    ], "vé điện tử đã bị tạo lỗi từ lần trước");

                } else {
                    return $this->returnResultServiceRepo->return_error_midle("Lỗi: bill.status=" . $check_bill[0]->status . " chưa định nghĩa");
                }

            } else if ($check_bill->count() > 1) {
                return $this->returnResultServiceRepo->return_error_midle("Lỗi: tạo nhiều lần vé điện tử cho ticket_id=" . $ticket_id);
            }

        } catch (\Throwable $ex) {
            return $this->returnResultServiceRepo->return_error_midle($ex->getMessage());
        }

        return $this->returnResultServiceRepo->return_success_midle(["return" => 0]);
    }


    /**
     *kiẻm tra bảng ticket : có tồn tại ticket_id , và có đang tạo bill ở luống khác không
     * @param $ticket_id
     * @return array : trả vê thông tin ticket
     */
    private function validate_create_invoice($ticket_id)
    {

        if (empty($ticket_id)) {
            $error = "Lỗi : dữ liệu gửi lên thiếu giá trị ticket_id";
            return $this->returnResultServiceRepo->return_error_midle($error);
        }
        //kiểm tra xem ticket này có đang trong quá trình tạo dở hay không -
        $ticket = $this->ticketRepository->getById($ticket_id);

        if ($ticket === null) {
            $error = "Không tồn tại vé ticket_id=" . $ticket_id;
            return $this->returnResultServiceRepo->return_error_midle($error);
        }

        if ($ticket->status_bill_id == 1) {
            $error = "vé này đang trong quá trình tạo vé điện tử ở request khác !";
            //chú ý mã 91
            return $this->returnResultServiceRepo->return_error_midle($error, 91);
        }
        return $this->returnResultServiceRepo->return_success_midle($ticket);
    }


    /**
     *  update trạng thái ticket đang tạo vé dt
     * @param $status : 1 là đang làm việc , 0 là không làm việc
     * @param $ticket_id
     * @return array
     */
    private function update_ticket_status_create_invoice($status, $ticket_id)
    {

        try {
            if (
                $this->ticketRepository->update($ticket_id, [
                    'status_bill_id' => $status,
                    'updated_at' => Carbon::now()
                ]) === false
            ) {

                throw new \Exception("Lỗi: update bảng ticket !");
            }
            ;

            $respone = $this->returnResultServiceRepo->return_success_midle();

        } catch (\Throwable $exception) {

            $this->logService->debug([$exception->getMessage()], __FUNCTION__);
            $respone = $this->returnResultServiceRepo->return_error_midle($exception->getMessage());
        }

        return $respone;
    }


    /**
     * hàm tạo vé điện tử , trung gian trong hàm create_invoice
     * @param $ticket_id
     * @return array
     */

    private function call_invoice_service($ticket)
    {
        try {
            $company = [
                "Name" => $this->companyName,
                "taxCode" => $this->taxCode
            ];
            
            //paymentItems------------------
            $paymentItems = [];
            if (!empty($ticket)) {

                $paymentItems[] = [
                    "Name" => $ticket["ticket_type_name"],
                    "Code" => $ticket["code"],
                    "Description" => $ticket["ticket_type_name"] . "-",
                    "UnitName" => config('invoice.paymentItems.UnitName'), //lay trong cau hinh
                    "Category" => $ticket["ticket_type_name"],
                    "Quantity" => config('invoice.paymentItems.Quantity'),//lay trong cau hinh
                    "UnitPrice" => $ticket["price"],
                    "TaxRate" => $this->ticket_taxRate
                ];

            } else {
                throw new Exception("Lỗi:  không tồn tại  ticket !");
            }

            //creator------------------
            $creator = [
                "id" => Auth::user()->id,
                "Name" => !empty(Auth::user()?->name) ? Auth::user()->name : "no_name"
            ];

            //buyer------------------

            //additionalData------------------
            $additionalData = [];

            // đối tượng mua : khách lẻ
            if (!empty($this->use_receipt)) {
                $invoiceConfiguration_type = Bill::TYPE_RECEIPT;
            } else {
                $invoiceConfiguration_type = Bill::TYPE_TICKET;
            }


            //-----Người mua ----------------
            $buyer = null;

            // ---invoiceConfiguration------
            $type = "ticket";
            $templateCode = $ticket->ticketType->templateCode;

            $invoiceConfiguration = [
                "templateCode" => $templateCode,
                "invoiceTypeCode" => "1", //cái này ko quan trọng , nhưng ko dc null và phải là string
                "symbolCode" => $templateCode,
                "Type" => $invoiceConfiguration_type
            ];
            //---------------------

            //tạo object invoice
            $send = $this->invoiceApiServiceRepo->CreatObjectInvoice($ticket->id, $paymentItems, $creator, $buyer, $additionalData, $invoiceConfiguration_type, $this->provider, $company, $invoiceConfiguration, $this->ticket_taxRate);

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

    /**
     * gọi api kiểm tra trạng thái
     * @param $order_id
     * @return array
     */
    private function get_invoice($ticket_id)
    {

        //tk login hddt
        $user_login = [
            "username" => config("invoice.api_login.param.username"),
            "password" => config("invoice.api_login.param.password"),
        ];

        
        $result = $this->invoiceApiServiceRepo->check_status_order($user_login, $ticket_id, $this->provider);

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
            "ticket_id" => $data["ticket_id"],

            "first_time" => $data["first_time"],
            "last_time" => $data["last_time"],
            "response_data" => $data["response_data"],
            "status" => $data["status"],
            "type" => $data["type"],

            "reservation_code" => $data["reservation_code"]??'',
            "seri_code" => $data["seri_code"]??'',
            "symbolCode" => $data["symbolCode"]??null,

            "is_delete" => 0,
            "created_at" => Carbon::now(),

        ];
        DB::beginTransaction();
        try {
            if ($this->billRepo->create($data) === false) {
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
    
}
