<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Services\InvoiceService;
use App\Services\ConfigService;
use App\Services\Invoice\ApiService;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class NewTicketInvoiceService
{

    public function __construct(
        protected ApiService $invoiceApiService,
        protected ConfigService $configService,
        protected InvoiceService $invoiceService,
        protected LogService $logService,
        protected OrderRepositoryInterface $orderRepo,
    ) {
    }

    public function invouiceTicketPending($order, $ticket_array)
    {
        $status = 200;
        $create_bill = $this->invoiceService->createBillBeforeOrder($order, $ticket_array);

        if ($create_bill['status'] != 200) {
            $status = 90;
            $message = 'Tạo biên lai thất bại';
        }
        $bills = $create_bill['data'];

        if (!empty($bills->ticket_id)) {
            $this->call_invoice_service($bills);
        } else {
            $this->call_invoice_service_order($bills);
        }

        // foreach ($bills as $key => $value) {
        //     if ((double) $value['ticket_data']['price'] > 0) {
        //         $this->call_invoice_service($value);
        //     }
        // }

        return [
            'status' => $status,
            'message' => $message ?? 'Tạo biên lai thành công'
        ];
    }

    public function call_invoice_service_order($bill)
    {
        // dd($bill);
        // dd($this->configService->getConfig(), 'config');

        $order = $this->orderRepo->getDataAllOption(['filter' => [['id', '=', $bill['order_id']]]], 0, ['booking.bookingDetail', 'shareSlotsBooking.booking.bookingDetail'])->first();
        $config = $this->configService->getConfig();

        $company = [
            "Name" => $config["companyName"] ?? null,
            "taxCode" => $config["taxCode"] ?? null
        ];

        $shareSlot = $order->shareSlotsBooking ?? null;

        $type = $order->type;

        if ($type === 3) {
            $booking = $shareSlot->booking;
            $UnitName = 'Người tham gia';
            $Quantity = $shareSlot?->slots ?? 0;
            $UnitPrice = $shareSlot?->booking->bookingDetail[0]->price ?? 0;
            $Description = json_decode($booking->note, true) ?? null;

        } else {
            $booking = $order->booking;
            $UnitName = 'Lượt đặt';
            $Quantity = 1;
            $UnitPrice = $order->amount;
            $Description = json_decode($booking->note, true) ?? null;
        }

        $bookingDetails = $booking->bookingDetail;


        if ($type == 2) {

            $sortedDetails = $bookingDetails->sortBy('date')->values();

            $firstDate = $sortedDetails->first()?->date;
            $lastDate = $sortedDetails->last()?->date;
            $bookingDate = 'từ ngày ' . Carbon::parse($firstDate)->format('d/m/Y') . ' đến ngày ' . Carbon::parse($lastDate)->format('d/m/Y');
        } else {

            $bookingDate = $booking->bookingDetail[0]->date ?? 0;
        }

        // ===== NAME =====
        $Name = match ($type) {
            3 => 'Tham gia sân ghép',
            1 => 'Thuê sân lẻ',
            default => 'Thuê sân cố định',
        };

        $paymentItems[] = [
            "Name" => $Name,
            "Code" => now()->format('dmYHis'),
            "Description" => $Description . '. Thời gian gia hạn ' . $bookingDate,
            "UnitName" => $UnitName,
            "Category" => $Name,
            "Quantity" => $Quantity,
            "UnitPrice" => $UnitPrice,
            "TaxRate" => null

            // "Name" => "40.000đ - BL Thu phí, lệ phí tham quan",
            // "Code" => "1923635941",
            // "Description" => "40.000đ - BL Thu phí, lệ phí tham quan-",
            // "UnitName" => "Vé",
            // "Category" => "40.000đ - BL Thu phí, lệ phí tham quan",
            // "Quantity" => 1,
            // "UnitPrice" => "40000.00",
            // "TaxRate" => null
        ];

        // dd($paymentItems);


        $creator = [
            "id" => $user->id ?? 'no_id',
            "Name" => $user->name ?? "no_name"
        ];

        $additionalData = [];

        $invoiceConfiguration_type = Invoice::TYPE_RECEIPT;

        //-----Người mua ----------------
        $buyer = [
            "Name" => "Khách hàng vãng lai",
            "Code" => "",
            "CompanyName" => "Khách hàng vãng lai"
        ];

        // $templateCode = "fake_template_code";

        $invoiceConfiguration = [
            "templateCode" => $config['invoiceConfiguration']['bill']['templateCode'] ?? null,
            "invoiceTypeCode" => "1",
            "symbolCode" => $config['invoiceConfiguration']['bill']['symbolCode'] ?? null,
            "Type" => $invoiceConfiguration_type
        ];

        $send = $this->invoiceApiService->CreatObjectInvoice(
            $bill->order_id,
            $paymentItems,
            $creator,
            $buyer,
            $additionalData,
            $invoiceConfiguration_type,
            $invoiceConfiguration["provider"] ?? "MISA",
            $company,
            $invoiceConfiguration,
            $ticketConfig["taxRate"] ?? null,
            $bill['created_at'] ?? null
        );

        $send['Webhook'] = $this->getWebhookUrl($bill);

        $send['code'] = $bill['reservation_code'];

        $send['ProfileCode'] = $config['invoiceConfiguration']['bill']['templateCode'] ?? null;

        $user_login = [
            "username" => config("invoice.api_login.param.username"),
            "password" => config("invoice.api_login.param.password"),
        ];

        $rs_invoiceService = $this->invoiceApiService->create_invoice(
            $user_login,
            $send,
            $invoiceConfiguration["provider"] ?? "MISA",
            0,
            true
        );

        if ($rs_invoiceService['status'] != 200) {
            $save_error_log = true;
            $rs_invoiceService['bill_id'] = $bill['id'];
            $rs_invoiceService['time_sent'] = Carbon::now()->format('Y-m-d H:i:s');
        } else {
            $save_error_log = false;
        }
    }

    public function call_invoice_service($bill)
    {
        // dd($bill);
        try {
            $ticket = $bill['ticket_data'];
            $config = $this->configService->getConfig();
            $ticketConfig = $invoiceConfiguration["ticket"] ?? [];
            $invoiceConfiguration = $config["invoiceConfiguration"] ?? [];
            $user = auth()->user();

            $company = [
                "Name" => $config["companyName"] ?? null,
                "taxCode" => $config["taxCode"] ?? null
            ];

            $paymentItems[] = [
                "Name" => $ticket["ticket_type_name"],
                "Code" => $ticket["code"],
                "Description" => $ticket["ticket_type_name"] . "-",
                "UnitName" => config('invoice.paymentItems.UnitName'), //lay trong cau hinh
                "Category" => $ticket["ticket_type_name"],
                "Quantity" => config('invoice.paymentItems.Quantity'),//lay trong cau hinh
                "UnitPrice" => $ticket["price"],
                "TaxRate" => $ticketConfig["taxRate"] ?? null
            ];
            //creator------------------
            $creator = [
                "id" => $user->id ?? 'no_id',
                "Name" => $user->name ?? "no_name"
            ];
            //buyer------------------

            //additionalData------------------
            $additionalData = [];

            // đối tượng mua : khách lẻ
            $invoiceConfiguration_type = Invoice::TYPE_RECEIPT;

            //-----Người mua ----------------
            $buyer = null;

            // ---invoiceConfiguration------
            $type = "ticket";
            $templateCode = $ticket->ticketType->templateCode;

            $invoiceConfiguration = [
                "templateCode" => $templateCode,
                "invoiceTypeCode" => "1", //cái này ko quan trọng , nhưng ko dc null và phải là string
                "symbolCode" => $templateCode,
                // "symbolCode" => 'AC-25E',

                "Type" => $invoiceConfiguration_type
            ];
            //---------------------

            //tạo object invoice
            $send = $this->invoiceApiService->CreatObjectInvoice(
                $ticket->id,
                $paymentItems,
                $creator,
                $buyer,
                $additionalData,
                $invoiceConfiguration_type,
                $invoiceConfiguration["provider"] ?? "MISA",
                $company,
                $invoiceConfiguration,
                $ticketConfig["taxRate"] ?? null,
                $bill['created_at'] ?? null
            );

            $send['Webhook'] = $this->getWebhookUrl($bill);

            $send['code'] = $bill['reservation_code'];

            $user_login = [
                "username" => config("invoice.api_login.param.username"),
                "password" => config("invoice.api_login.param.password"),
            ];

            $rs_invoiceService = $this->invoiceApiService->create_invoice(
                $user_login,
                $send,
                $invoiceConfiguration["provider"] ?? "MISA",
                0,
                true
            );

            if ($rs_invoiceService['status'] != 200) {
                $save_error_log = true;
                $rs_invoiceService['bill_id'] = $bill['id'];
                $rs_invoiceService['time_sent'] = Carbon::now()->format('Y-m-d H:i:s');
            } else {
                $save_error_log = false;
            }

        } catch (\Throwable $th) {
            $save_error_log = true;
            $rs_invoiceService = [];
            $rs_invoiceService['bill_id'] = $bill['id'];
            $rs_invoiceService['message'] = $th->getMessage();
            $rs_invoiceService['time_sent'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        if ($save_error_log) {
            // dd($rs_invoiceService, 'sent_bill_log');
            $this->logService->savedebug($rs_invoiceService, 'sent_bill_log');
        }

    }

    private function getWebhookUrl($bill)
    {
        $key = Crypt::encrypt($bill['id']);
        $url = url('api/v1/billCallBack/' . $key);
        if (strpos($url, 'localhost') !== false) {
            $url = str_replace('localhost', config('invoice.ip_callback') ?? 'localhost', $url);
        }
        return $url;
    }
}
