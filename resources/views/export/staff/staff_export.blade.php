<table class="table-bordered table-responsive">
    <thead>

    <tr>
        <td colspan="9" align="left" valign="middle" style="font-size:12px; text-align: center">
            <p><b>Lotte Westlake Tây Hồ</b></p>
        </td>
    </tr>

    <tr>
        <td colspan="9" align="center" valign="middle" style="font-size:16px"><b>BÁO CÁO NHÂN VIÊN</b></td>
    </tr>

    <tr>
        <td colspan="9" align="center" valign="middle"><i>Ngày báo cáo : {{$date_report}}</i></td>
    </tr>

    <tr>
        <td colspan="9" align="left" valign="middle">{{session('is_report_have_image')}}</td>
    </tr>
    {{--    hàng 7--}}
    {{--    <tr>--}}

    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}

    {{--        <td align="center" valign="middle" ><b>Từ tìm kiếm</b></td>--}}
    {{--        <td align="center" valign="middle" >{{$key_search}}</td>--}}

    {{--        <td align="center" valign="middle" ><b>Giải đấu</b></td>--}}
    {{--        <td align="center" valign="middle" >{{$tournament_name"}}</td>--}}
    {{--        <td align="center" valign="middle" ><b>Đơn vị</b></td>--}}
    {{--        <td align="center" valign="middle" >{{$participant_type_name}}</td>--}}
    {{--        <td align="center" valign="middle" ><b>Ngày sinh</b></td>--}}
    {{--        <td align="center" valign="middle" >{{$athlete_birdthday}}</td>--}}

    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}



    {{--    </tr>--}}
    {{--    <tr>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}


    {{--        <td align="center" valign="middle" ><b>Đối tượng</b></td>--}}
    {{--        <td align="center" valign="middle" >{{$participant_type_name}}</td>--}}
    {{--        <td align="center" valign="middle" ><b>Khối</b></td>--}}
    {{--        <td align="center" valign="middle" >{{$participant_sub_group_name}}</td>--}}
    {{--        <td align="center" valign="middle" ><b>Giới tính</b></td>--}}
    {{--        <td align="center" valign="middle" >--}}

    {{--            <?php if(isset($athlete_gender) and $athlete_gender != ""){--}}
    {{--                if((int)($athlete_gender)==1)--}}
    {{--                    echo "Nam";--}}
    {{--                else if((int)($athlete_gender)==2)--}}
    {{--                    echo "Nữ";--}}
    {{--            }--}}
    {{--             ?>--}}

    {{--        </td>--}}


    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}


    {{--    </tr>--}}

    {{--    <tr>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}


    {{--        <td align="center" valign="middle" ><b>Loại</b></td>--}}
    {{--        <td align="center" valign="middle" >{{$athlete_type_name}}</td>--}}
    {{--        <td align="center" valign="middle" ><b>Lớp</b></td>--}}
    {{--        <td align="center" valign="middle" >{{$parcitipant_class_name}}</td>--}}
    {{--        <td align="center" valign="middle" ><b>Thông tin chính xác</b></td>--}}
    {{--        <td align="center" valign="middle" >{{$is_check_false_name}}</td>--}}


    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}
    {{--        <td align="center" valign="middle" ></td>--}}


    {{--    </tr>--}}

    {{--    <tr>--}}
    {{--        <td colspan="9" align="left" valign="middle"  ></td>--}}
    {{--    </tr>--}}


    {{--    <tr>--}}
    {{--        <td colspan="2" ><b>TỔNG SỐ NGƯỜI ĐĂNG KÝ THI ĐẤU:</b></td>--}}
    {{--        <td style="text-align:left;color:red;"><b>{{$total_athlate}}</b></td>--}}
    {{--        <td colspan="8" ></td>--}}
    {{--        <td style="text-align:center;color:red;"></td>--}}
    {{--        <td style="text-align:center;color:red;"></td>--}}
    {{--        <td style="text-align:center;color:red;"></td>--}}

    {{--    </tr>--}}
    {{--    <tr>--}}
    {{--        <td colspan="3" ><b>Nội dung đăng ký thi đấu (Người):</b></td>--}}
    {{--        <td style="text-align:left;color:red;"></td>--}}
    {{--        <td colspan="7" ></td>--}}
    {{--        <td style="text-align:center;color:red;"></td>--}}
    {{--        <td style="text-align:center;color:red;"></td>--}}
    {{--        <td style="text-align:center;color:red;"></td>--}}

    {{--    </tr>--}}
    {{--   @isset($ar_tournamentCompetition)--}}
    {{--       @foreach($ar_tournamentCompetition as $item)--}}
    {{--    <tr>--}}
    {{--        <td colspan="2" >-{{$item["name"]}}</td>--}}
    {{--        <td style="text-align:left;color:red;"><b>{{$item["total"]}}</b></td>--}}
    {{--        <td style="text-align:center;color:red;"></td>--}}
    {{--        <td colspan="6" ></td>--}}
    {{--        <td style="text-align:center;color:red;"></td>--}}
    {{--        <td style="text-align:center;color:red;"></td>--}}
    {{--        <td style="text-align:center;color:red;"></td>--}}
    {{--        <td style="text-align:center;color:red;"></td>--}}

    {{--    </tr>--}}
    {{--       @endforeach--}}
    {{--   @endisset--}}
    {{--vòng for môn bơi--}}

    <tr>
        <td colspan="9" align="left" valign="middle"></td>
    </tr>

    <tr>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">STT
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã thẻ
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã nhân viên
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Họ và tên
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Công ty
        </td>
{{--        <td align="center" valign="middle"--}}
{{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Phòng ban--}}
{{--        </td>--}}
{{--        <td align="center" valign="middle"--}}
{{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Chức vụ--}}
{{--        </td>--}}
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Giới tính
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Điện thoại
        </td>
        <td align="center" valign="middle"
            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Ảnh
        </td>


        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Nội dung đăng ký thi đấu--}}
        {{--        </td>--}}
        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Họ và tên VĐV--}}
        {{--        </td>--}}
        {{--        <?php if (null !== session('is_report_have_image') and session('is_report_have_image') == "true")  { ?>--}}
        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Ảnh chân dung--}}
        {{--        </td>--}}
        {{--        <?php } ?>--}}
        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Giới tính--}}
        {{--        </td>--}}

        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Ngày/tháng/năm sinh--}}
        {{--        </td>--}}
        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã VĐV--}}
        {{--        </td>--}}
        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Chiều cao (cm)--}}
        {{--        </td>--}}
        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle "> Cân nặng (kg)--}}
        {{--        </td>--}}
        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Họ và tên phụ huynh--}}
        {{--        </td>--}}

        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">SĐT phụ huynh--}}
        {{--        </td>--}}
        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Trường--}}
        {{--        </td>--}}
        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Khối--}}
        {{--        </td>--}}
        {{--        <td align="center" valign="middle"--}}
        {{--            style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Lớp--}}
        {{--        </td>--}}
    </tr>

    </thead>
    <tbody style="border: 1px solid">
    @isset($staffs)
        @foreach($staffs as $key => $staff)
            <tr>
                <td style="text-align: center; white-space: normal; vertical-align: top;">{{$key + 1}}</td>

                <td class="sort" style="text-align: center;vertical-align: top;">
                    {{"`".@(string)$staff['card_id']}}
                </td>

                <td class="sort" style="text-align: center;vertical-align: top;">
                    {{@$staff['code']}}
                </td>

                <td class="sort" style="text-align: center;vertical-align: top;">
                    {{@$staff['name']}}
                </td>
                <td class="sort" style="text-align: center;vertical-align: top;">
                    {{@$staff->company["name"]}}
                </td>
{{--                <td class="sort" style="text-align: center;vertical-align: top;">--}}
{{--                    {{@$staff->department['name']}}--}}
{{--                </td>--}}
{{--                <td class="sort" style="text-align: center;vertical-align: top;">--}}
{{--                    {{@$staff->position['name']}}--}}
{{--                </td>--}}
                <td class="sort" style="text-align: center;vertical-align: top;">
                        <?php
                        if (isset($staff['gender'])) {
                            if ((int)$staff['gender'] == 0) echo "Nữ";
                            else if ((int)$staff['gender'] == 1) echo "Nam";
                        }
                        ?>
                </td>
                <td class="sort" style="text-align: center;vertical-align: top;">
                    {{@$staff->position['phone']}}
                </td>
                <td class="sort" style="text-align: center;vertical-align: top;">
                    Anh
                </td>


                {{--                <td class="sort"--}}
                {{--                    style="text-align: center;vertical-align: top;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?>">{{$athlete['competition_name']}}</td>--}}
                {{--                <td class="sort"--}}
                {{--                    style="text-align: center;vertical-align: top;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?> ">{{$athlete['name']}}</td>--}}

                {{--                    <?php if (null !== session('is_report_have_image') and session('is_report_have_image') == "true")  { ?>--}}
                {{--                <td class="sort"--}}
                {{--                    style="text-align: center;vertical-align: top ;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?>"></td>--}}
                {{--                <?php } ?>--}}
                {{--                <td class="sort"--}}
                {{--                    style="text-align: center;vertical-align: top ;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?>">{{$athlete['gender']=="1"?"Nam":"Nữ"}}</td>--}}
                {{--                <td class="sort"--}}
                {{--                    style="text-align: center; vertical-align: top;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?> ">--}}
                {{--                    {{(isset($athlete['birthday_day']) and strlen($athlete['birthday_day'])==2)? $athlete['birthday_day']:"  "}}--}}
                {{--                    -{{(isset($athlete['birthday_month']) and strlen($athlete['birthday_month'])==2)?$athlete['birthday_month']:"  "}}--}}
                {{--                    -{{(isset($athlete['birthday_year']) and strlen($athlete['birthday_year'])==4)? $athlete['birthday_year']:"  "}}--}}
                {{--                </td>--}}

                {{--                <td class="sort"--}}
                {{--                    style="text-align: center;vertical-align: top;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?> ">{{$athlete['athlete_code']}}</td>--}}
                {{--                <td class="sort"--}}
                {{--                    style="text-align: center;vertical-align: top ;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?>">{{$athlete['height']}}</td>--}}
                {{--                <td class="sort"--}}
                {{--                    style="text-align: center;vertical-align: top ;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?>">{{$athlete['weight']}}</td>--}}

                {{--                <td class="sort"--}}
                {{--                    style="text-align: center; vertical-align: top ;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?>">{{isset($athlete['guardian_name'])? $athlete['guardian_name'] : ''}}</td>--}}
                {{--                --}}{{--                <td class="sort" style="text-align: center; vertical-align: top ">--}}
                {{--                --}}{{--                                    <?php if(isset($athlete['guardian'][0]['participant_relationship'])) {--}}
                {{--                --}}{{--                                             $participant_relationship = $athlete['guardian'][0]['participant_relationship'];--}}
                {{--                --}}{{--                                            if($participant_relationship=="1") echo "Ông";--}}
                {{--                --}}{{--                                            else if($participant_relationship=="2") echo "Bà";--}}
                {{--                --}}{{--                                            else if($participant_relationship=="3") echo "Bố";--}}
                {{--                --}}{{--                                            else if($participant_relationship=="4") echo "Mẹ";--}}
                {{--                --}}{{--                                            else if($participant_relationship=="5") echo "Cô";--}}
                {{--                --}}{{--                                            else if($participant_relationship=="6") echo "Dì";--}}
                {{--                --}}{{--                                            else if($participant_relationship=="7") echo "Chú";--}}
                {{--                --}}{{--                                            else if($participant_relationship=="8") echo "Bác";--}}
                {{--                --}}{{--                                            else if($participant_relationship=="9") echo "Cậu";--}}
                {{--                --}}{{--                                            else if($participant_relationship=="10") echo "Anh";--}}
                {{--                --}}{{--                                            else if($participant_relationship=="11") echo "Chị";--}}
                {{--                --}}{{--                                            else if($participant_relationship=="12") echo "Em";--}}

                {{--                --}}{{--                                   } ?>--}}
                {{--                --}}{{--                                </td>--}}
                {{--                <td class="sort"--}}
                {{--                    style="text-align: center; vertical-align: top ;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?>">{{isset($athlete['guardian_phone'])? $athlete['guardian_phone'] : ''}}</td>--}}


                {{--                <td style="text-align: center; white-space: normal; vertical-align: top;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?>">{{$athlete['participant_group_name']}}</td>--}}
                {{--                <td style="text-align: center; white-space: normal; vertical-align: top;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?>">{{$athlete['parcitipant_sub_group_name']}}</td>--}}
                {{--                <td style="text-align: center; white-space: normal; vertical-align: top;<?php if($athlete["css"]=="0") echo 'background-color: yellow';?>">{{$athlete['parcitipant_class_name']}}</td>--}}

            </tr>

        @endforeach
    @endisset
    </tbody>
</table>

