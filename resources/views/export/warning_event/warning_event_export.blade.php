<table class="table-bordered table-responsive">
    <thead>

    <tr>
        <td colspan="8" align="left" valign="middle" style="font-size:12px; text-align: center">
            <!-- <p><b>KVC Ô Quy Hồ</b></p> -->
        </td>
    </tr>

    <tr>
        <td colspan="8" align="center" valign="middle" style="font-size:16px"><b>BÁO CÁO SỰ KIỆN CẢNH BÁO</b></td>
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
        <td colspan="1" align="center" valign="middle">{{session("search.key_search")}}</td>
        <td colspan="1" align="left" valign="middle"></td>
        <td colspan="1" align="center" valign="middle"><b>Khu vực</b></td>
        <td colspan="1" align="center" valign="middle">{{session("search.area_name")}}</td>
        <td colspan="2" align="left" valign="middle"></td>
    </tr>

    <tr>
        <td colspan="1" align="left" valign="middle"></td>
        <td colspan="1" align="center" valign="middle"><b>Dịch vụ</b></td>
        <td colspan="1" align="center" valign="middle">{{session("search.service_name")}}</td>
        <td colspan="1" align="left" valign="middle"></td>
        <td colspan="1" align="center" valign="middle"><b>Điểm vui chơi</b></td>
        <td colspan="1" align="center" valign="middle">{{ session('search.fun_spot_name')}}</td>
        <td colspan="2" align="left" valign="middle"></td>
    </tr>

    <tr>
        <td colspan="1" align="left" valign="middle"></td>
        <td colspan="1" align="center" valign="middle"><b>Từ ngày</b></td>
        <td colspan="1" align="center" valign="middle">{{session("search.start_date")}}</td>
        <td colspan="1" align="left" valign="middle"></td>
        <td colspan="1" align="center" valign="middle"><b>Đến ngày</b></td>
        <td colspan="1" align="center" valign="middle">{{session("search.end_date")}}</td>
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
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã cảnh báo
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã vé
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Loại vé
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Khu vực
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Dịch vụ
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mô tả
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Thời hạn sử dụng
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Ngày sử dụng
        </td>
    </tr>

    </thead>
    <tbody style="border: 1px solid">
    @isset($data)
        @foreach($data as $key => $item)
            <tr>
                <td style="text-align: center; white-space: normal; vertical-align: top;">{{$key + 1}}</td>
                <td style="text-align: center; white-space: normal; vertical-align: top;">
                        <?php
                        if ((int)$item['event_code'] == 91) {
                            echo 'Vé không tồn tại';
                        } elseif ((int)$item['event_code'] == 92) {
                            echo 'Có vé nhưng sử dụng sai dịch vụ';
                        } elseif ((int)$item['event_code'] == 93) {
                            echo 'Chưa đúng ngày sử dụng (vé chưa có ngày , hoặc quá hạn ,  hoặc chưa đến)';
                        } elseif ((int)$item['event_code'] == 94) {
                            echo 'Vé đã sử dụng rồi';
                        } elseif ((int)$item['event_code'] == 90) {
                            echo 'Lỗi chung';
                        } else {
                            echo 'Chưa định nghĩa';
                        }
                        ?>
                </td>
                <td style="text-align: center; white-space: normal; vertical-align: top;">
                    @if(!empty(@$item['ticket']["code"]))
                            {{ @$item['ticket']["code"] }}
                    @endif
                </td>
                <td style="text-align: center; white-space: normal; vertical-align: top;">
                    @if(!empty(@$item['ticket']["ticket_type_name"]))
                        {{ @$item['ticket']["ticket_type_name"] }}
                    @endif
                </td>
                <td style="text-align: center; white-space: normal; vertical-align: top;">{{ $item->getNameArea()}}</td>
                <td style="text-align: center; white-space: normal; vertical-align: top;">
                    @if((int)$item["type"]!=1)
                        {{ @$item["type_name"] }}
                    @endif
                </td>
                <td style="text-align: center; white-space: normal; vertical-align: top;">
                    @if (substr($item['description'],0,8)=="Lỗi : ")
                        {{ucfirst(substr($item['description'],8))}}
                    @else
                        {{ $item['description']}}
                    @endif
                </td>
                <td style="text-align: center; white-space: normal; vertical-align: top;">
                    {{ substr(@$item['ticket']["use_date"],0,10) }}

                </td>
                <td style="text-align: center; white-space: normal; vertical-align: top;">{{ $item['created_at'] }}</td>

            </tr>
        @endforeach
    @endisset
    </tbody>
</table>

