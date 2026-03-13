<table class="table-bordered table-responsive">
    <thead>

        <tr>
            <td colspan="6" align="left" valign="middle" style="font-size:12px; text-align: center">
                <!-- <p><b>KVC Ô Quy Hồ</b></p> -->
            </td>
        </tr>

        <tr>
            <td colspan="6" align="center" valign="middle" style="font-size:16px"><b>BÁO CÁO DANH THU THEO ĐỐI TƯỢNG
                    KHÁCH HÀNG</b>
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
                {{ substr(session('reportWithCustomerClassification,start_date'), 8, 2) , '-' , substr(session('reportWithCustomerClassification,start_date'), 5, 2) , '-' , substr(session('reportWithCustomerClassification,start_date'), 0, 4) }}
            </td>
            <td colspan="1" align="center" valign="middle">Đến ngày</td>
            <td colspan="1" align="center" valign="middle">
                {{ substr(session('reportWithCustomerClassification,end_date'), 8, 2) , '-' , substr(session('reportWithCustomerClassification,end_date'), 5, 2) , '-' , substr(session('reportWithCustomerClassification,end_date'), 0, 4) }}
            </td>
        </tr>


        <tr>
            <td colspan="6" align="left" valign="middle"></td>
        </tr>

        <tr>
            <th align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">STT
            </th>
            <th align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tên đối tượng KH</th>

            <th align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Số vé bán được (vé)</th>

            <th align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tổng tiền tạm tính(đ)
            </th>

            <th align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tổng tiền chiết khấu (đ)
            </th>

            <th align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tổng tiền thực thu (đ)
            </th>

        </tr>

    </thead>
    <tbody class="list form-check-all">
        @php
            $totalTicket = 0;
            $totalRealAmount = 0;
            $totalAmount = 0;
            $totalDiscount = 0;
        @endphp
        @foreach ($data as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $item['customer_classification_name'] }}</td>
                <td class="text-center">{{ $item['countTicket'] }}</td>
                <td style="text-align: right">
                    {{ number_format($item['sumRealAmount'], 0, ',', ',') }}</td>
                <td style="text-align: right">
                    {{ number_format($item['discount'], 0, ',', ',') }}</td>
                <td style="text-align: right">
                    {{ number_format($item['sumAmount'], 0, ',', ',') }}</td>
            </tr>
            @php
                $totalTicket += $item['countTicket'];
                $totalRealAmount += $item['sumRealAmount'];
                $totalAmount += $item['sumAmount'];
                $totalDiscount += $item['discount'];
            @endphp
        @endforeach

        <tr style="background-color: rgb(0, 85, 0);color:#fff">
            <td style="text-align: center; background-color: rgb(0, 85, 0);color:#fff" colspan="2"><b>Tổng</b></td>
            <td style="background-color: rgb(0, 85, 0);color:#fff">{{ $totalTicket }}</td>
            <td style="text-align: right;background-color: rgb(0, 85, 0);color:#fff">
                {{ number_format($totalRealAmount, 0, ',', ',') }}</td>
            <td style="text-align: right;background-color: rgb(0, 85, 0);color:#fff">
                {{ number_format($totalDiscount, 0, ',', ',') }}</td>
            <td style="text-align: right;background-color: rgb(0, 85, 0);color:#fff">
                {{ number_format($totalAmount, 0, ',', ',') }}</td>
        </tr>
    </tbody>
</table>
