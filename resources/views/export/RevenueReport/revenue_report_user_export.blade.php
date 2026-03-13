<table class="table-bordered table-responsive">
    <thead>

        <tr>
            <td colspan="6" align="left" valign="middle" style="font-size:12px; text-align: center">
                <!-- <p><b>KVC Ô Quy Hồ</b></p> -->
            </td>
        </tr>

        <tr>
            <td colspan="6" align="center" valign="middle" style="font-size:16px"><b>BÁO CÁO DANH THU THEO NGƯỜI BÁN</b>
            </td>
        </tr>

        <tr>
            <td colspan="6" align="center" valign="middle"><i>Ngày báo cáo : {{ date('G:i d-m-Y') }}</i></td>
        </tr>


        <tr>
            <td colspan="6" align="left" valign="middle"></td>
        </tr>

        <tr>
            <td colspan="1" align="center" valign="middle"></td>
            <td colspan="1" align="center" valign="middle">Từ ngày</td>
            <td colspan="1" align="center" valign="middle">
                {{ substr(session('search.start_date'), 8, 2) . '-' . substr(session('search.start_date'), 5, 2) . '-' . substr(session('search.start_date'), 0, 4) }}
            </td>
            <td colspan="1" align="center" valign="middle">Đến ngày</td>
            <td colspan="1" align="center" valign="middle">
                {{ substr(session('search.end_date'), 8, 2) . '-' . substr(session('search.end_date'), 5, 2) . '-' . substr(session('search.end_date'), 0, 4) }}
            </td>
        </tr>


        <tr>
            <td colspan="6" align="left" valign="middle"></td>
        </tr>


        <tr>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">STT
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Người bán
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Số vé bán được (vé)
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tổng tiền tạm tính (đ)
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Chiết khấu (đ)
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tổng tiền thực thu
                khấu (đ)
            </td>
        </tr>

    </thead>
    <tbody>
        @php
            $i = 1;
        @endphp

        @isset($data)
            @foreach ($data['data'] as $key => $item)
                <tr>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $i++ }}</td>
                    <td style="text-align: left; white-space: normal; vertical-align: middle;">{{ $key }} </td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $item['count_ticket'] }}
                    </td>
                    <td style="text-align: right; white-space: normal; vertical-align: middle;">
                        {{ number_format($item['total_amount'], 0, '.', ',') }}</td>
                    <td style="text-align: right; white-space: normal; vertical-align: middle;">
                        {{ number_format($item['total_discount'], 0, '.', ',') }}</td>
                    <td style="text-align: right; white-space: normal; vertical-align: middle;">
                        {{ number_format($item['total_real_amount'], 0, '.', ',') }}</td>
                </tr>
            @endforeach

            <tr style="background-color: rgb(0, 85, 0);color:#fff">
                <td colspan="2" class="text-center"
                    style="text-align: center; white-space: normal; vertical-align: middle;"><b>Tổng </b></td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b> {{ $data['sum']['all_ticket'] }} </b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b> {{ number_format($data['sum']['all_real_amount'], 0, '.', ',') }} </b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b> {{ number_format($data['sum']['all_discount'], 0, '.', ',') }} </b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b> {{ number_format($data['sum']['all_amount'], 0, '.', ',') }} </b>
                </td>
            </tr>
        @endisset

    </tbody>
</table>
