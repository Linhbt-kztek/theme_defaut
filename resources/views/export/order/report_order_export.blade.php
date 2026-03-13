@php
    if (!function_exists('discount')) {
        function discount($data_function)
        {
            if ($data_function->type_of_rate == 2) {
                $discount = $data_function->rate * count($data_function->tickets);
            } elseif ($data_function->type_of_rate == 1) {
                $discount = ($data_function->rate * $data_function->amount) / 100;
            } else {
                $discount = 0;
            }

            return $discount;
            // return number_format($discount, 0, '.', '.') . 'đ';
        }
    }

    //session tim kiem
    $status_invoice = '';
    if ((int) session('search.invoice_status') == 1) {
        $status_invoice = 'Đã tạo';
    } elseif ((int) session('search.invoice_status') == 2) {
        $status_invoice = 'Tạo lỗi';
    }

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
                <i>Bộ lọc: {{ session('order_search.date') }}</i>
            </td>
        </tr>
        <tr>
            {{-- <td align="center" valign="middle"></td>
            <td align="center" valign="middle"><b>Mã hóa đơn</b></td>
            <td align="center" valign="middle">{{ session('search.code_order') }}</td>
            <td align="center" valign="middle"><b>Đối tượng KH</b></td>
            <td align="left" valign="middle">{{ session('search.customer_classification_name') }}</td>
            <td align="center" valign="middle"><b>Hình thức TT</b></td>
            <td align="center" valign="middle">{{ session('search.payment_methods_name') }}</td>
            <td align="center" valign="middle"><b>Trạng thái TT</b></td>
            <td align="center" valign="middle">{{ session('search.payment_status_name') }}</td>
            <td align="center" valign="middle"><b>Từ</b></td>
            <td align="center" valign="middle">
                {{ \Carbon\Carbon::parse(session('search.start_date'))->format('d/m/Y') }}</td>
            <td align="center" valign="middle"></td>
            <td align="center" valign="middle"></td> --}}
        </tr>
        <tr>
            {{-- <td align="center" valign="middle"></td>
            <td align="center" valign="middle"></td>
            <td align="center" valign="middle"></td>
            <td align="center" valign="middle"><b>Người bán</b></td>
            <td align="left" valign="middle">{{ session('search.user_name') }}</td>
            <td align="center" valign="middle"><b>Hình thức mua</b></td>
            <td align="center" valign="middle">{{ session('search.type_name') }}</td>
            <td align="center" valign="middle"><b>Trạng thái HĐ/vé ĐT</b></td>
            <td align="center" valign="middle">{{ $status_invoice }}</td>
            <td align="center" valign="middle"><b>Đến</b></td>
            <td align="center" valign="middle">{{ \Carbon\Carbon::parse(session('search.end_date'))->format('d/m/Y') }}
            </td>
            <td align="center" valign="middle"></td>
            <td align="center" valign="middle"></td> --}}
        </tr>
        <tr>
            <td colspan="{{ $col }}" align="left" valign="middle"></td>
        </tr>

        <tr>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">STT
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã hoá đơn
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tiền tạm tính
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Chiết khấu
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tiền thực thu
            </td>
            {{-- <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Đối tượng KH
            </td> --}}
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Trạng thái TT
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Hình thức mua
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Hình thức TT
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Người bán
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Ngày mua
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Ngày hết hạn
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Trạng thái HĐ/Vé/Biên lai ĐT
            </td>
        </tr>

    </thead>
    <tbody>
        @foreach ($data as $key => $order)
            <tr>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $key + 1 }}</td>

                <td style="white-space: normal; vertical-align: middle;">{{ $order->code_order }} </td>

                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    {{ number_format($order->real_amount ?? '', 0, '.', '.') . 'đ' }}</td>


                <td style="white-space: normal; vertical-align: middle;text-align: right">
                    {{ number_format($order->discount ?? '', 0, '.', '.') . 'đ' }}</td>

                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    {{ number_format($order->amount ?? '', 0, '.', '.') . 'đ' }}</td>

                {{-- <td style="white-space: normal; vertical-align: middle;">
                    {{ $order->customerClassification->name ?? '' }}</td> --}}
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    @if ($order->payment_status == 1)
                        {{ 'Chưa thanh toán' }}
                    @elseif($order->payment_status == 2)
                        {{ 'Đã thanh toán' }}
                    @elseif($order->payment_status == 3)
                        {{ 'Đã huỷ' }}
                    @endif
                </td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    {{ $order->type == 1 ? 'Tại quầy' : 'Trực tuyến' }}</td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    {{ $order->payment_methods == 1 ? 'Tiền mặt' : ($order->payment_methods == 3 ? 'Thẻ' : 'Online') }}
                </td>
                <td style="text-align: left; white-space: normal; vertical-align: middle;">
                    {{ $order->user->user_name ?? '' }}</td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    {{ $order->expire_date ? \Carbon\Carbon::parse($order->expire_date)->format('d/m/Y') : '' }}</td>
                <td
                    style="text-align: center; white-space: normal; vertical-align: middle;{{ $order['styleInvoice'] }}">
                    {{ $order['statusInvoice'] }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
