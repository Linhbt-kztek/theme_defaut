<table class="table-bordered table-responsive">
    <thead>

        <tr>
            <td colspan="10" align="left" valign="middle" style="font-size:12px; text-align: center">
                <!-- <p><b>KVC Ô Quy Hồ</b></p> -->
            </td>
        </tr>

        <tr>
            <td colspan="10" align="center" valign="middle" style="font-size:16px"><b>BÁO CÁO HIỆU SUẤT CHƯƠNG TRÌNH
                    KHUYẾN MÃI</b>
            </td>
        </tr>

        <tr>
            <td colspan="10" align="center" valign="middle"><i>Ngày báo cáo : {{ date('G:i d-m-Y') }}</i></td>
        </tr>


        <tr>
            <td colspan="10" align="center" valign="middle"> Dữ liệu từ:
                {{ substr(session('search.start_date'), 8, 2) . '-' . substr(session('search.start_date'), 5, 2) . '-' . substr(session('search.start_date'), 0, 4) }}
                đến
                {{ substr(session('search.end_date'), 8, 2) . '-' . substr(session('search.end_date'), 5, 2) . '-' . substr(session('search.end_date'), 0, 4) }}
            </td>
        </tr>

        <tr>
            <!-- <td colspan="1" align="center" valign="middle"></td>
            <td colspan="1" align="center" valign="middle">Từ ngày</td>
            <td colspan="1" align="center" valign="middle">
                {{ substr(session('search.start_date'), 8, 2) . '-' . substr(session('search.start_date'), 5, 2) . '-' . substr(session('search.start_date'), 0, 4) }}
            </td> -->
        </tr>
        <tr>
            <!-- <td colspan="1" align="center" valign="middle"></td>
            <td colspan="1" align="center" valign="middle">Đến ngày</td>
            <td colspan="1" align="center" valign="middle">
                {{ substr(session('search.end_date'), 8, 2) . '-' . substr(session('search.end_date'), 5, 2) . '-' . substr(session('search.end_date'), 0, 4) }}
            </td> -->

        </tr>


        <tr>
            <td colspan="9" align="left" valign="middle"></td>
        </tr>


        <tr>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">STT
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tên chương trình khuyến
                mãi
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mô tả
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Ngày bắt đầu
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Ngày kết thúc
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Đã sử dụng
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Giới hạn sử dụng
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tạm tính
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Giảm giá
            </td>
             <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Thực thu
            </td>

        </tr>

    </thead>
    <tbody>

        @isset($data)
            @php
                $index = 0;
            @endphp

            @foreach ($data as $keyPromotional => $value)
                @php
                    $index++;
                    $ordersWithDiscounts = collect();
                    $totalOrderAmount = 0;
                    $totalDiscount = 0;
                    $ordersWithDiscounts = $value->discounts->filter(fn($discount) => $discount->order);

                    $totalOrderAmount = $ordersWithDiscounts->sum(fn($discount) => $discount->order->real_amount);
                    $totalDiscount = $ordersWithDiscounts->sum(fn($discount) => $discount->order->discount);
                    $usedCount = 0;
                    foreach ($value->discounts as $discount_order) {

                        if (empty($discount_order->order == null)) {
                            $usedCount++;

                        }
                    }

                    foreach ($promotionals as $val) {
                        if ($val->id == $keyPromotional) {
                            $thisPromotionalType = $val;
                            break;
                        }
                        $rate = $val->rate ?? 0;
                        $limited = $value->limited == null ? 'Không giới hạn' : $value->limited;
                        $totalUse = $value->count();
                        $maxUsage = $value->limited == null ? '-' : $value->limited;
                    }

                    $desc = '';

                    if ($value->condition_type === 1 && $value->type === 1) {
                        $desc = 'Giảm ' . $value->rate . '% cho đơn hàng từ ' . number_format($value->condition_value) . 'đ';
                    } elseif ($value->condition_type === 1 && $value->type === 2) {
                        $desc = 'Giảm ' . number_format($value->rate) . 'đ cho đơn hàng mua từ  ' . number_format($value->condition_value) . ' sản phẩm';
                    } elseif ($value->condition_type === 2 && $value->type === 1) {
                        $desc = 'Giảm ' . $value->rate . '% cho sản phẩm từ ' . number_format($value->condition_value) . 'đ';
                    } else {
                        $desc = 'Giảm ' . number_format($value->rate) . 'đ cho đơn hàng mua từ ' . number_format($value->condition_value) . ' sản phẩm';
                    }



                @endphp
                <tr>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $index }}</td>
                    <td style="text-align: left; white-space: normal; vertical-align: middle;">
                        {{ $value->name }}
                    </td>
                    <td style="text-align: left; white-space: normal; vertical-align: middle;">{{  $desc }}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $value->expiry_date_from}}
                    </td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $value->expiry_date_to}}
                    </td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{  $usedCount }}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{  $maxUsage }}</td>
                    <td style="text-align: right; white-space: normal; vertical-align: middle;">{{  $totalDiscount + $totalOrderAmount }}</td>
                    <td style="text-align: right; white-space: normal; vertical-align: middle;">{{  $totalDiscount }}</td>
                    <td style="text-align: right; white-space: normal; vertical-align: middle;">{{  $totalOrderAmount }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="9" style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b>Tổng tiền tạm tính</b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b>{{ number_format($real_amount, 0, '.', ',') }}</b>
                </td>
            </tr>
            <tr>
                <td colspan="9" style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b>Tổng tiền chiết khâu</b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b>{{ number_format($discount, 0, '.', ',') }}</b>
                </td>
            </tr>
            <tr style="background-color: rgb(0, 85, 0);color:#fff">
                <td colspan="9" style="text-align: center; white-space: normal; vertical-align: middle;">
                    <b>Tổng tiền thực thu</b>
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    <b>{{ number_format($amount, 0, '.', ',') }}</b>
                </td>
            </tr>
        @endisset
    </tbody>
    @dd(132);
</table>