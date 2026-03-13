@php
    $name_days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
    $workingTimeConfig = $checkTurn['workingTimeConfig'];
    $dataCheck = $checkTurn['data'];
@endphp

<div class="col-md-9 set_background">
    <div style="margin-top:5px">
        <div style="float: right">
            <span style="margin-right: 15px;">
                <b><i style="color: #d3d3d3" class="ri-record-circle-line align-middle"></i>
                    Không có lịch làm việc</b>
            </span>
            <span style="margin-right: 15px;">
                <b><i class="ri-record-circle-line align-middle text-secondary"></i>
                    Chưa chấm công</b>
            </span>
            <span style="margin-right: 15px;">
                <b><i class="ri-record-circle-line align-middle text-success"></i>
                    Đi làm đúng giờ</b>
            </span>
            <span style="margin-right: 15px;">
                <b><i class="ri-record-circle-line align-middle text-warning"></i>
                    Đi làm muộn</b>
            </span>
            <span style="margin-right: 15px;">
                <b><i class="ri-record-circle-line align-middle text-danger"></i>
                    Không đi làm</b>
            </span>
            <span style="margin-right: 15px;">
                <b><i class="ri-record-circle-line align-middle text-purple"></i>
                    Nghỉ lễ</b>
            </span>
        </div>
    </div>
    <br>
    <div class="bg-opacity-50 rounded-2xl p-3">
        <div class="d-flex justify-content-around text-lg row">
            @foreach ($name_days as $name_day)
                <div class="col text-center">
                    <div class="justify-content-center title-color">
                        <b class="">{{ $name_day }}</b>
                    </div>
                </div>
            @endforeach
        </div>
        @foreach ($data['allDays'] as $week)
            <div class="row">
                @foreach ($week as $day)
                    @if (!empty($workingTimeConfig[$day['date_format']]) && count(@$workingTimeConfig[$day['date_format']]) > 0)
                        @php
                            $list_checkIn = [];
                            $countWorkingTimeConfig = $workingTimeConfig[$day['date_format']];
                            $list_checkIn['status'] = true;
                            foreach ($workingTimeConfig[$day['date_format']] as $ke => $va) {
                                if (!empty($dataCheck[$day['date_format']][$va->id]['checkin'])) {
                                    $list_checkIn[$day['date_format']][] =
                                        $dataCheck[$day['date_format']][$va->id]['checkin'];

                                    $list_checkIn[$day['date_format']][] =
                                        $dataCheck[$day['date_format']][$va->id]['checkout'] ?? [];

                                    if ($dataCheck[$day['date_format']][$va->id]['checkin']['status'] != 1) {
                                        $list_checkIn['status'] = false;
                                    }
                                }
                            }
                        @endphp

                        @if (!empty($list_checkIn[$day['date_format']]))
                            <div class="col text-center">
                                <div
                                    class="set-height position-relative d-flex justify-content-end align-items-start rounded-lg bg-white shadow-lg in_month">
                                    <button date_format="{{ $day['date_format'] }}"
                                        class="btn btn-sm custom_day {{ in_array($day['date_format'], $day_off) ? 'purple' : (@$list_checkIn['status'] ? 'btn-success' : 'btn-warning') }}">{{ $day['date'] }}</button>

                                    <div class="position-absolute bottom-0 end-0 w-100 d-flex">
                                        <table>
                                            <tr>
                                                @php
                                                    $count = 0;
                                                @endphp
                                                @foreach ($list_checkIn[$day['date_format']] as $item)
                                                    <td>
                                                        {{-- <p> --}}
                                                        {{-- @php
                                                                switch ($item['created_on'] ?? 0) {
                                                                    case 1:
                                                                        $class = 'ri-door-open-line';
                                                                        break;
                                                                    case 2:
                                                                        $class = 'ri-smartphone-line';
                                                                        break;
                                                                    case 3:
                                                                        $class = 'ri-global-line';
                                                                        break;
                                                                    default:
                                                                        $class = 'ri-question-line';
                                                                        break;
                                                                }
                                                            @endphp

                                                            <i class="{{ $class }} align-items-center"></i> --}}
                                                        <b>
                                                            {{ ($count > 0 ? ' - ' : '') . ($item['time_action'] ?? '.. : ..') }}
                                                        </b>
                                                        {{-- </p> --}}
                                                    </td>
                                                    @php
                                                        $count++;
                                                    @endphp
                                                @endforeach
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col text-center">
                                <div
                                    class="set-height position-relative d-flex justify-content-end align-items-start rounded-lg bg-white shadow-lg in_month">
                                    <button date_format="{{ $day['date_format'] }}"
                                        class="btn btn-sm custom_day {{ in_array($day['date_format'], $day_off) ? 'purple' : ($day['day_status'] ? 'btn-danger' : 'btn-info') }}">{{ $day['date'] }}</button>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="col text-center">
                            <div
                                class="set-height position-relative d-flex justify-content-end align-items-start rounded-lg bg-white shadow-lg {{ !$day['in_month'] ? 'not_in_month' : '' }} ">
                                <b class="default_day">{{ $day['date'] }}</b>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>
</div>
