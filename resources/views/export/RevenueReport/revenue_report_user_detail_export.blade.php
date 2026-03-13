@php $col_span=9;  @endphp
<table class="table-bordered table-responsive">
    <thead>

        <tr>
            <td colspan="{{$col_span}}" align="left" valign="middle" style="font-size:12px; text-align: center">
                <!-- <p><b>KVC Ô Quy Hồ</b></p> -->
            </td>
        </tr>

        <tr>
            <td colspan="{{$col_span}}" align="center" valign="middle" style="font-size:16px"><b>BÁO CÁO TỔNG HỢP</b>
            </td>
        </tr>

        <tr>
            <td colspan="{{$col_span}}" align="center" valign="middle"><i>Ngày báo cáo : {{ date('G:i d-m-Y') }}</i></td>
        </tr>


        <tr>
            <td colspan="{{$col_span}}" align="left" valign="middle"></td>
        </tr>

        <tr>
            <td colspan="1" align="center" valign="middle"></td>
            <td colspan="1" align="center" valign="middle"></td>

            <td colspan="1" align="right" valign="middle"><b>Từ ngày</b></td>
            <td colspan="1" align="center" valign="middle">
                {{ substr(session('search.start_date'), 8, 2) . '-' . substr(session('search.start_date'), 5, 2) . '-' . substr(session('search.start_date'), 0, 4) }}
            </td>

            <td colspan="1" align="center" valign="middle"></td>

            <td colspan="1" align="center" valign="middle"><b>Đến ngày</b></td>
            <td colspan="1" align="center" valign="middle">
                {{ substr(session('search.end_date'), 8, 2) . '-' . substr(session('search.end_date'), 5, 2) . '-' . substr(session('search.end_date'), 0, 4) }}
            </td>
            <td colspan="1" align="center" valign="middle"></td>
            <td colspan="1" align="center" valign="middle"></td>
        </tr>


        <tr>
            <td colspan="{{$col_span}}" align="left" valign="middle"></td>
        </tr>


        <tr>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">STT
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Người bán
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Loại vé
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Số vé bán được(vé)
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tiền tạm tính(đ)
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tổng vé bán được(vé)
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tổng tiền tạm tính(đ)
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Chiết khấu(đ)
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tổng tiền thực thu(đ)

            </td>
        </tr>

    </thead>
    <tbody class="list form-check-all">
    @php
        $i = 0;
        $total_ticket_all =0;
        $total_real_amount_all =0;
        $total_discount_all =0;
        $total_amount_all =0;
    @endphp
    {{--duyệt từng nguoi ban ve--}}
    @foreach ($data['data'] as $key => $item)

        @php
            $i++;
            $total_ticket_type_ticket[$key] =0;
        @endphp

        @foreach ($item as $key_1 => $type_ticket)
            @php
                $total_ticket_type_ticket[$key] += (int)$type_ticket->total_tickets ;
            @endphp
        @endforeach

        {{--duyệt từng loai ve--}}
        @foreach ($item as $key_1 => $type_ticket)
            <tr>
                @if($key_1==0)
                    <td rowspan="{{$item->count()}}" align="center"
                        valign="middle"> {{ $i}}</td>
                    <td rowspan="{{$item->count()}}" align="center"
                        valign="middle">
                        <b>{{$data['sum_user'][$key]["user_name"]}}</b></td>
                @endif
                <td align="left">{{$type_ticket->ticket_type_name}}</td>
                <td align="right"
                    valign="middle">{{$type_ticket->total_tickets}}</td>
                <td align="right">{{number_format($type_ticket->total_price, 0, '.', '.')}}</td>
                @if($key_1==0)
                        <td align="right" valign="middle" rowspan="{{$item->count()}}"><b>{{number_format($total_ticket_type_ticket[$key], 0, ',', '.')}}</b></td>

                    <td align="right" valign="middle" rowspan="{{$item->count()}}"><b>{{number_format($data['sum_user'][$key]["total_real_amount"], 0, '.', '.')}}</b></td>

                    <td align="right" valign="middle" rowspan="{{$item->count()}}"><b>{{number_format($data['sum_user'][$key]["total_discount"], 0, '.', '.')}}</b></td>
                    <td align="right" valign="middle" rowspan="{{$item->count()}}"><b>{{number_format($data['sum_user'][$key]["total_amount"], 0, '.', '.')}}</b></td>
                @endif
            </tr>
        @endforeach
        @php
            $total_ticket_all += $total_ticket_type_ticket[$key];
            $total_real_amount_all += $data['sum_user'][$key]["total_real_amount"];
            $total_discount_all += $data['sum_user'][$key]["total_discount"];
            $total_amount_all += $data['sum_user'][$key]["total_amount"];
        @endphp
    @endforeach

    <tr >
        <td colspan="5" align="center" valign="middle" style="background-color:#006400;color:white;font-weight:bold;">
            Tổng
        </td>
        <td align="right" valign="middle"
            style="background-color:#006400;color:white;font-weight:bold;">{{number_format($total_ticket_all, 0, '.', '.')}}</td>
        <td align="right" valign="middle"
            style="background-color:#006400;color:white;font-weight:bold;">{{number_format($total_real_amount_all, 0, '.', '.')}}</td>
        <td align="right" valign="middle"
            style="background-color:#006400;color:white;font-weight:bold;">{{number_format($total_discount_all, 0, '.', '.')}}</td>
        <td align="right" valign="middle"
            style="background-color:#006400;color:white;font-weight:bold;">{{number_format($total_amount_all, 0, '.', '.')}}</td>

    </tr>
    </tbody>
</table>
