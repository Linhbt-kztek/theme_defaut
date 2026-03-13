@php
    $colspan =5;
@endphp
<table class="table-bordered table-responsive">
    <thead>
    <tr>
        <td colspan="{{$colspan}}" align="left" valign="middle" style="font-size:12px; text-align: center">
            <!-- <p><b>KVC Ô Quy Hồ</b></p> -->
        </td>
    </tr>

    <tr>
        <td colspan="{{$colspan}}" align="center" valign="middle" style="font-size:16px"><b>HÓA ĐƠN</b>
        </td>
    </tr>

    <tr>
        <td colspan="{{$colspan}}" align="center" valign="middle"><i>Ngày xuất : {{ date('G:i d-m-Y') }}</i></td>
    </tr>

    <tr>
        <td colspan="{{$colspan}}" align="left" valign="middle"></td>
    </tr>


    <tr>
        <td align="center" style="color:red;">Mã hóa đơn: </td>
        <td  align="left" style="color:red;">#{{ $order->code_order }}</td>
        <td align="center">Số lượng: </td>
        <td align="left">{{ count($tickets) }} vé</td>
        <td></td>
    </tr>


    <tr>
        <td align="center">Thời gian đặt:</td>
        <td  align="left"> {{ \Carbon\Carbon::parse($order->created_at)->format('H:i d/m/Y') }}</td>
        <td align="center">Thời hạn đến hết ngày:</td>
        <td align="left"> {{ \Carbon\Carbon::parse($order->expire_date)->format('d/m/Y') }}</td>
        <td></td>
    </tr>

    <tr>
        <td colspan="{{$colspan}}" align="left" valign="middle"></td>
    </tr>

    <tr>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">STT
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã vé
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tên vé
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Trạng thái
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Giá (đ)
        </td>
    </tr>
    </thead>

    <tbody>
    @foreach ($tickets as $key => $ticket)
        @php
            if (!empty($ticket->expire_date)) {
                $expireDate = \Carbon\Carbon::parse($ticket->expire_date);
                $has_expire_date = true;
                $expire_date = $expireDate->isPast();
            } else {
                $has_expire_date = false;
                $expire_date = false;
            }
        @endphp

        <tr>
            <td align="center" valign="middle">{{ $key + 1 }}</td>
            <td align="center"><strong>{{ $ticket->code }}</strong></td>
            <td>{{ $ticket->ticket_type_name}}</td>

            <td align="center" valign="middle">
                @if (count($ticket->events) > 0)
                    <span class="badge bg-danger">Đã sử dụng</span>
                @elseif($has_expire_date)
                    @if ($expire_date)
                        <span class="badge bg-success">Hết hạn</span>
                    @else
                        <span class="badge bg-success">Chưa sử dụng</span>
                    @endif
                @else
                    <span class="badge bg-success">Chưa sử dụng</span>
                @endif
            </td>
            <td align="right" valign="middle">{{ number_format($ticket->price, 0, '.', ',') }}</td>
        </tr>

    @endforeach

    <tr>
        <td colspan="4"><b>Tạm tính:</b></td>
        <td align="right">{{ number_format($order->real_amount, 0, '.', ',')  }}</td>
    </tr>
    <tr>
        <td colspan="4"><b>Giảm giá/chiết khấu:</b></td>
        <td align="right"> {{ number_format($order->discount, 0, '.', ',') }}</td>
    </tr>
    <tr>
        <td colspan="4"><b>Tổng cộng:</b></td>
        <td align="right">{{ number_format($order->amount, 0, '.', ',') }}</td>
    </tr>

    </tbody>

</table>
