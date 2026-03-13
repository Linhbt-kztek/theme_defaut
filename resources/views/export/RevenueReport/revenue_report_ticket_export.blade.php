<table class="table-bordered table-responsive">
    <thead>
        <tr>
            <td colspan="5" align="left" valign="middle" style="font-size:12px; text-align: center">
                <!-- <p><b>KVC Ô Quy Hồ</b></p> -->
            </td>
        </tr>

        <tr>
            <td colspan="5" align="center" valign="middle" style="font-size:16px"><b>BÁO CÁO DANH THU THEO LOẠI VÉ</b>
            </td>
        </tr>

        <tr>
            <td colspan="5" align="center" valign="middle"><i>Ngày báo cáo : {{ date('G:i d-m-Y') }}</i></td>
        </tr>

        <tr>
            <td colspan="5" align="left" valign="middle"></td>
        </tr>

        <tr>
            <td colspan="1" align="center" valign="middle"></td>
            <td colspan="1" align="center" valign="middle">Từ ngày</td>
            <td colspan="3" align="center" valign="middle">
                {{ substr(session('search.start_date'), 8, 2) . '-' . substr(session('search.start_date'), 5, 2) . '-' . substr(session('search.start_date'), 0, 4) }}
            </td>
        </tr>
        <tr>
            <td colspan="1" align="center" valign="middle"></td>
            <td colspan="1" align="center" valign="middle">Đến ngày</td>
            <td colspan="3" align="center" valign="middle">
                {{ substr(session('search.end_date'), 8, 2) . '-' . substr(session('search.end_date'), 5, 2) . '-' . substr(session('search.end_date'), 0, 4) }}
            </td>
        </tr>

        @if(!empty($filteredTicketTypeNames))
            <tr>
                <td colspan="1" align="center" valign="middle"></td>
                <td colspan="1" align="center" valign="middle">Lọc theo loại vé</td>
                <td colspan="3" align="left" valign="middle">
                    {{ implode(', ', $filteredTicketTypeNames) }}
                </td>
            </tr>
        @endif

        <tr>
            <td colspan="5" align="left" valign="middle"></td>
        </tr>

        <tr>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">STT
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tên vé
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Giá vé offline
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Số lượng (vé)
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Thành tiền
            </td>
        </tr>

    </thead>
    <tbody>
        @isset($data)
            @php
                $index = 0;
                $countOfTicket = 0;
                $total_money = 0;
            @endphp
            @foreach ($data as $keyTicket => $value)
                @php
                    $index++;
                    $thisTicketType = null;
                    foreach ($ticketType as $val) {
                        if ($val->id == $keyTicket) {
                            $thisTicketType = $val;
                            break;
                        }
                    }

                    // Tính thành tiền dựa trên giá thực tế của từng vé
                    $subtotal = $value->sum('price');
                    $total_money += $subtotal;
                    $countOfTicket += count($value);
                    $soldQuantity = count($value);
                @endphp
                <tr>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $index }}</td>
                    <td style="text-align: left; white-space: normal; vertical-align: middle;">
                        {{ @$thisTicketType->name }}
                    </td>
                    <td style="text-align: right; white-space: normal; vertical-align: middle;">
                        {{ number_format(@$thisTicketType->price_offline, 0, '.', ',') }}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $soldQuantity }}</td>
                    <td style="text-align: right; white-space: normal; vertical-align: middle;">
                        {{ number_format($subtotal, 0, '.', ',') }}</td>
                </tr>
            @endforeach


            <tr style="background-color: rgb(163, 163, 163);">
                <td colspan="3" style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b>Tổng</b>
                </td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b>{{ $countOfTicket }}</b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b>{{ number_format($total_money, 0, '.', ',') }}</b>
                </td>
            </tr>

            <!-- <tr>
                <td colspan="4" style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b>Tổng tiền tạm tính</b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b>{{ number_format($real_amount, 0, '.', ',') }}</b>
                </td>
            </tr>

            <tr>
                <td colspan="4" style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b>Tổng tiền chiết khấu</b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b>{{ number_format($discount, 0, '.', ',') }}</b>
                </td>
            </tr>

            <tr style="background-color: rgb(0, 85, 0);color:#fff">
                <td colspan="4" style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b>Tổng tiền thực thu</b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b>{{ number_format($amount, 0, '.', ',') }}</b>
                </td>
            </tr> -->
        @endisset
    </tbody>
</table>