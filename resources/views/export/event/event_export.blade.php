<table class="table-bordered table-responsive">
 
    <thead>
        <?php
            $keySearch = session("searchEvent.key_search");
            $type = session("searchEvent.type") == 1 ? 'Nhân viên' : 'Khách mời';
            $lane = session("searchEvent.lane") == 1 ? 'Làn vào' : 'Làn ra';

            ?>
        <tr>
            <td colspan="8" align="left" valign="middle" style="font-size:12px; text-align: center">
                <!-- <p><b>KVC Ô Quy Hồ</b></p> -->
            </td>
        </tr>

        <tr>
            <td colspan="8" align="center" valign="middle" style="font-size:30px"><b>BÁO CÁO SỰ KIỆN VÀO</b></td>
        </tr>

        <tr>
            <td colspan="8" align="center" valign="middle"><i>Ngày báo cáo : {{date("G:i d-m-Y")}}</i></td>
        </tr>

        <tr>
            <td colspan="8" align="left" valign="middle"></td>
        </tr>

        <tr>
            <td colspan="1" align="left" valign="middle"></td>
            <td colspan="1" align="center" valign="middle"><b>Từ khoá</b></td>
            <td colspan="1" align="center" valign="middle">{{  $keySearch ?? 'Không có'}}</td>
            <td colspan="1" align="left" valign="middle"></td>
            <td colspan="1" align="center" valign="middle"><b>Phân loại</b></td>
            <td colspan="1" align="center" valign="middle">{{session("searchEvent.type") ?  $type : 'Tất cả' }}</td>
            <td colspan="2" align="left" valign="middle"></td>
        </tr>

        <tr>
            <td colspan="1" align="left" valign="middle"></td>
            <td colspan="1" align="center" valign="middle"><b>Phòng</b></td>
            <td colspan="1" align="center" valign="middle">{{$search_room_name}}</td>
            <td colspan="1" align="left" valign="middle"></td>
            <td colspan="1" align="center" valign="middle"><b>Làn</b></td>
            <td colspan="1" align="center" valign="middle">{{  session("searchEvent.lane") ? $lane : 'Tất cả'}}
            </td>
            <td colspan="2" align="left" valign="middle"></td>
        </tr>

        <tr>
            <td colspan="1" align="left" valign="middle"></td>
            <td colspan="1" align="center" valign="middle"><b>Thời gian</b></td>
            <td colspan="2" align="center" valign="middle">{{session("searchEvent.date")}}</td>
            <td colspan="2" align="left" valign="middle"></td>
        </tr>

        <tr>
            <td colspan="8" align="left" valign="middle"></td>
        </tr>

        <tr>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">STT
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Họ và tên
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Phân loại
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Chức vụ
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tên đơn vị cơ sở
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Phòng
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Làn
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Thời gian
            </td>
        </tr>

    </thead>
    <tbody style="border: 1px solid">
        @isset($data)
            @foreach($data as $key => $item)
                <?php

                // Xác định loại đối tượng
                $isStaff = $item['object_type'] == 1;
                $isGuest = $item['object_type'] == 2;

                // Lấy tên
                $name = $isStaff
                    ? ($item['staff']['name'] ?? '')
                    : ($item['guest']['name'] ?? '');

                // Lấy chức vụ
                $position = $isStaff
                    ? ($item['staff']['position'] ?? '')
                    : ($item['guest']['position'] ?? '');

                // Lấy tên đơn vị/cơ sở
                $agency = $isStaff
                    ? ($item['staff']['agency']['name'] ?? '')
                    : ($item['guest']['agencies_value'] ?? '');

                // Phân loại
                $typeLabel = $isStaff ? 'Thành viên' : 'Khách mời';

                // Phòng
                $roomName = $item['room']['name'] ?? '';

                // Làn
                $lane = $item['lane'] == 1 ? 'Làn vào' : 'Làn ra';

                // Thời gian
                $time = \Carbon\Carbon::parse($item['time_action'])->format('H:i:s d/m/Y ');

                // Phương thức định danh
                $identityMethods = [
                    1 => 'Khuôn mặt',
                    2 => 'Vân tay',
                    3 => 'Thẻ từ',
                ];
                $identity = $identityMethods[$item['identity_method']] ?? '';
                                ?>
                <tr>
                    <td style="text-align: center; white-space: normal; vertical-align: top;">{{$key + 1}}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: top;">{{ $name }}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: top;"> {{ $typeLabel}}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: top;">{{ $position}}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: top;">{{$agency}}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: top;">{{ $roomName}}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: top;">{{ $lane }}</td>
                    <td style="text-align: center; white-space: normal; vertical-align: top;">{{$time }}</td>
                </tr>
            @endforeach
        @endisset
    </tbody>
</table>