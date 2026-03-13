@php


    //session tim kiem
    $status_invoice = '';
    if ((int) session('search.invoice_status') == 1) {
        $status_invoice = 'Đã tạo';
    } elseif ((int) session('search.invoice_status') == 2) {
        $status_invoice = 'Tạo lỗi';
    }
    $now = \Carbon\Carbon::now();
@endphp
@php($col = 13)
<table class="table-bordered table-responsive">
    <thead>
        <tr>
            <td colspan="{{ $col }}" align="left" valign="middle" style="font-size:12px; text-align: center">
                <p><b>{{ $config_login['title_web'] ?? config('software.title') }}</b></p>
            </td>
        </tr>
        <tr>
            <td colspan="{{ $col }}" align="center" valign="middle" style="font-size:16px"><b>BÁO CÁO TỔNG HỢP HÓA
                    ĐƠN</b>
            </td>
        </tr>
        <tr>
            <td colspan="{{ $col }}" align="center" valign="middle"><i>Ngày báo cáo :
                    {{ date('G:i d-m-Y') }}</i></td>
        </tr>
        <tr>
            <td colspan="{{ $col }}" align="center" valign="middle">
                <i>Bộ lọc: {{ session('order.date') }}</i>
            </td>
        </tr>

        <tr>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">STT
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã hoá đơn
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã lịch đặt
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Thành tiền
            </td>


            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Trạng thái thanh toán
            </td>

            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Hình thức thanh toán
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Người bán
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Thời gian tạo
            </td>

            <!-- <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Trạng thái HĐ/Vé/Biên lai
                ĐT
            </td> -->
        </tr>

    </thead>
    <tbody>
        @foreach ($data as $key => $order)
            <tr>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $key + 1 }}</td>

                <td style="white-space: normal; vertical-align: middle;">{{ $order->code }} </td>

                <td style="white-space: normal; vertical-align: middle;">
                    {{ optional($order->booking)->code ?? optional($order->shareSlotsBooking->booking)->code }}
                </td>


                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    {{ number_format($order->amount ?? 0, 0, '.', '.') . 'đ' }}
                </td>

                <td class="text-center">
                    @if ($order->status == 2)
                        <span>Đã hoàn</span>

                    @elseif ($order->status == 1 && $order->activation_date)

                        @if (\Carbon\Carbon::parse($order->activation_date)->isPast())

                            @if ($order->bill_success == 1)
                                <span>Đã gửi HĐĐT</span>
                            @else
                                <span>Đã thanh toán</span>
                            @endif

                        @else
                            <span>Đặt cọc</span>
                        @endif

                    @endif
                </td>

                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    {{ $order->payment_method == 1 ? 'Tiền mặt' : 'Chuyển khoản online' }}
                </td>
                <td style="text-align: left; white-space: normal; vertical-align: middle;">
                    {{ $order->createdBy->user_name ?? '' }}
                </td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    {{ \Carbon\Carbon::parse($order->created_at)->format('H:i d/m/Y') }}
                </td>
                <!-- <td style="text-align: center; white-space: normal; vertical-align: middle;{{ $order['styleInvoice'] }}">
                                {{ $order['statusInvoice'] }}
                            </td> -->
            </tr>
        @endforeach

    </tbody>
</table>