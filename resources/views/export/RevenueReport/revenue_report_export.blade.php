@php
    function revenueColor($value)
    {
        if ($value >= 10000000)
            return '#81c784';   // xanh nhạt vừa
        if ($value >= 5000000)
            return '#a5d6a7';   // xanh pastel
        if ($value >= 1000000)
            return '#c8e6c9';   // xanh rất nhạt
        if ($value >= 500000)
            return '#e8f5e9';   // gần trắng
        if ($value > 0)
            return '#f1f8e9';   // cực nhạt
        return '#ffffff';
    }

    $courts = $data['courts'];

    // danh sách ngày trong tháng (lấy từ sân đầu tiên)
    $days = array_keys($courts[0]['daily_amount']);

    $courtSummaryMap = collect($data['court_summary'])
        ->keyBy('court_id');


@endphp

<table class="table table-bordered table-responsive" style="font-size:13px">
    <thead>
        <tr>
            <th colspan="{{ count($days) + 1 }}" style="text-align:center; font-size:16px">
                <b>BÁO CÁO DOANH THU THÁNG {{ $data['month'] }}</b>
            </th>
        </tr>

        <tr>
            <th colspan="{{ count($days) + 1 }}" style="text-align:center">
                <i>Ngày báo cáo: {{ date('H:i d-m-Y') }}</i>
            </th>
        </tr>

        <tr>
            <th colspan="{{ count($days) + 1 }}"></th>
        </tr>

        {{-- Header --}}
        <tr style="background:#e2efd9">
            <th style="white-space:nowrap">Sân \ Ngày</th>
            @foreach($days as $day)
                <th style="text-align:center">
                    {{ \Carbon\Carbon::parse($day)->format('d/m') }}
                </th>
            @endforeach

            <th style="text-align:center; background:#c8e6c9">
                Doanh thu từng sân
            </th>
        </tr>
    </thead>

    <tbody>

        {{-- Mỗi hàng là 1 sân --}}
        @foreach($courts as $court)
            @php
                $summary = $courtSummaryMap[$court['court_id']] ?? null;
                $totalMonth = $summary['total_amount'] ?? 0;
            @endphp

            <tr>
                <td style="white-space:nowrap">
                    {{ $court['court_name'] }}
                </td>

                @foreach($days as $day)
                    @php
                        $value = $court['daily_amount'][$day] ?? 0;
                    @endphp
                    <td style="
                                text-align:right;
                                background-color: {{ revenueColor($value) }};
                                color:#1b5e20;
                            ">
                        {{ $value > 0 ? number_format($value) : '0' }}
                    </td>
                @endforeach

                {{-- Tổng doanh thu tháng theo sân --}}
                <td style="
                    text-align:right;
                    font-weight:bold;
                    background:#e8f5e9;
                    color:#1b5e20;
                ">
                    {{ number_format($totalMonth) }}
                </td>
            </tr>
        @endforeach

    </tbody>

    {{-- Tổng theo ngày --}}
    <tfoot>
        <tr style="background:#004d40; color:#fff; font-weight:bold">
            <td class="text-start">Doanh thu/ngày</td>

            @foreach($days as $day)
                @php
                    $sumDay = collect($courts)->sum(fn($c) => $c['daily_amount'][$day] ?? 0);
                @endphp
                <td style="text-align:right">
                    {{ $sumDay > 0 ? number_format($sumDay) : '0' }}
                </td>
            @endforeach

            <td>{{ number_format($data['revenue']) }} đ</td> {{-- cột tổng tháng --}}
        </tr>
    </tfoot>

</table>
