<table class="table-bordered table-responsive">
    <thead class="bg-white">
        <tr>
            <td colspan="12" align="left" valign="middle" style="font-size:12px; text-align: center">
                <!-- <p><b>KVC Ô Quy Hồ</b></p> -->
            </td>
        </tr>

        <tr>
            <td colspan="12" align="center" valign="middle" style="font-size:24px"><b>BÁO CÁO ƯU ĐÃI MÃ GIẢM GIÁ</b>
            </td>
        </tr>

        <tr>
            <td colspan="12" align="center" valign="middle"><i>Ngày báo cáo : {{ date('G:i d-m-Y') }}</i></td>
        </tr>

        <tr>
            <td colspan="12" align="center" valign="middle">
                Dữ liệu từ:
                {{ substr(session('search.start_date'), 8, 2) . '-' . substr(session('search.start_date'), 5, 2) . '-' . substr(session('search.start_date'), 0, 4) }}
                đến
                {{ substr(session('search.end_date'), 8, 2) . '-' . substr(session('search.end_date'), 5, 2) . '-' . substr(session('search.end_date'), 0, 4) }}
            </td>
        </tr>

        <tr>
            <td colspan="12" align="left" valign="middle"></td>
        </tr>
        <tr>
            <td colspan="12" align="left" valign="middle"></td>
        </tr>
        <tr>
            <td colspan="12" align="left" valign="middle"></td>

        <tr>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">STT</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">Tên voucher</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">Mô tả</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">Ngày bắt đầu</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">Ngày kết thúc</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">Số lượng mã </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">Tổng lượt cấp phát</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">Số lượt sử dụng</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">Trạng thái</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">Giảm giá</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">Tạm tính</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle">Thực thu</td>
        </tr>
    </thead>
    <tbody>
        @if(isset($data))

            @php
                $index = 1;
                $totalDiscount = 0;
                $totalRealAmount = 0;
            @endphp

            @foreach ($data as $voucherId => $voucher)
                @php
                    $name = $voucher->voucher_name ?? $voucher->name ?? '---';
                    $type = $voucher->type == 1 ? '%' : 'đ';
                    $rate = $voucher->rate ?? 0;
                    $description = $voucher->description ?? '';
                    $count = $voucher->count ?? 0;
                    $limited = $voucher->limited ?? 0;
                    $condition = $voucher->condition_value ?? '';

            
                
                    $usedCount = 0;
                    $maxUsage = 0;
                    $totalDiscount = 0;
                    $totalOrderAmount = 0;
                    $voucherTotalAmount = 0;
                    $voucherTotalDiscount = 0;

                    foreach ($voucher->voucher_detail as $voucher_detail) {
                        foreach ($voucher_detail->discount as $discount_order) {
                            $voucherTotalDiscount = $discount_order->order['discount'] ?? 0;
                            $voucherTotalAmount = $discount_order->order['real_amount'] ?? 0;
                            $usedCount++;
                        }

                    }

                    $totalDiscount += $voucherTotalDiscount;
                    $totalRealAmount += $voucherTotalAmount;
                    $maxUsage = $voucher->limited != null ? $count * $voucher->limited : '-';

                    // Xác định trạng thái
                    $today = now();
                    $from = \Carbon\Carbon::parse($voucher->expiry_date_from);
                    $to = \Carbon\Carbon::parse($voucher->expiry_date_to);

                    if ($to->lt($today)) {
                        $status = 'Hết hạn';
                    } elseif ($limited > 0 && $usedCount >= $limited) {
                        $status = 'Đã dùng hết';
                    } else {
                        $status = 'Còn hạn';
                    }
               
                @endphp

                <tr>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $index }}</td>
                    <td style="text-align: left; white-space: normal; vertical-align: middle;">{{ $name }}</td>
                    <td style="text-align: left; white-space: normal; vertical-align: middle;">Giảm {{ $rate }}{{ $type }} cho
                        đơn hàng giá trị trên {{  number_format($condition) }}đ </td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">
                        {{ $voucher->expiry_date_from ?? '' }}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">
                        {{ $voucher->expiry_date_to ?? '' }}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $count }}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $maxUsage }}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $usedCount }}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $status }}</td>
                    <td style="text-align: right; white-space: normal; vertical-align: middle;">
                        {{ number_format($voucherTotalDiscount, 0, '.', ',') }}</td>
                    <td style="text-align: right; white-space: normal; vertical-align: middle;">
                        {{ number_format($voucherTotalAmount, 0, '.', ',') }}</td>

                    <td style="text-align: right; white-space: normal; vertical-align: middle;">
                        {{ number_format($voucherTotalDiscount, 0, '.', ',') }}</td>

                </tr>

                @php
                    $index++;
                @endphp
            @endforeach

            <tr>
                <td colspan="11" style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b>Tổng tiền tạm tính</b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b>{{ number_format($real_amount ?? $totalRealAmount, 0, '.', ',') }}</b>
                </td>
            </tr>
            <tr>
                <td colspan="11" style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b>Tổng tiền chiết khấu</b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b>{{ number_format($discount ?? $totalDiscount, 0, '.', ',') }}</b>
                </td>
            </tr>
            <tr style="background-color: rgb(0, 85, 0);color:#fff">
                <td colspan="11" style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b>Tổng tiền thực thu</b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b>{{ number_format($amount ?? ($totalRealAmount - $totalDiscount), 0, '.', ',') }}</b>
                </td>
            </tr>
        @endif
    </tbody>
</table>
