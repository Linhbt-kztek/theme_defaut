<table class="table-bordered table-responsive">
    <thead>

        <tr>
            <td colspan="6" align="left" valign="middle" style="font-size:12px; text-align: center">
                <!-- <p><b>KVC Ô Quy Hồ</b></p> -->
            </td>
        </tr>

        <tr>
            <td colspan="6" align="center" valign="middle" style="font-size:16px"><b>BÁO CÁO THỜI GIAN SỬ DỤNG</b>
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
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tên phòng
            </td>

            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Số lượt sử sử dụng
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Thời gian sử dụng
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tỷ suất
            </td>
    
        </tr>

    </thead>
    <tbody>
        @php
            $i = 1;
        @endphp
        @isset($data)
            @foreach ($data as $key => $item)
                <tr>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $i++ }}</td>
                    <td style="text-align: left; white-space: normal; vertical-align: middle;">{{$item['room_name'] }} </td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $item['total_time_formatted'] }}
                    </td>
                    <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $item['turn_count'] }}
                    </td>
                    <td style="text-align: right; white-space: normal; vertical-align: middle;">
                        {{ number_format($item['turn_percent'], 2, '.', ',') }}%</td>
                </tr>
            @endforeach
        @endisset

    </tbody>
</table>
