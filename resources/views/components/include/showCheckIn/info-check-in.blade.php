<div class="col-md-3">
    <div class="bg-opacity-50 rounded-2xl shadow-lg p-3 set_background h-100">
        <div class="profile-card" style="background-color: #ffffff00 !important">
            <div class="image-container">
                <img src="{{ $staff->img_avt }}" alt="Profile Picture">
                <h3>{{ $staff->name }}</h3>
            </div>
        </div>
        <div class="border-top border-bottom border-dark text-center p-1 d-flex justify-content-evenly">
            <b>{{ $staff->code }}</b>
            <span>
                <i class="bx bxs-bullseye"></i>
            </span>
            <b>{{ $staff->department->name ?? '' }}</b>
        </div>
        <div class="mb-3">
            <ul class="list-unstyled">
                <li class="text-primary d-flex justify-content-between">
                    <p>Trạng thái chốt công</p>
                    @if ($checkTurn['hasTurn'])
                        <p class="fw-semibold text-success">Đã chốt công</p>
                    @else
                        <p class="fw-semibold text-danger">Chưa chốt công</p>
                    @endif
                </li>
                <li class="text-primary d-flex justify-content-between">
                    <p>Công</p>
                    <p class="fw-semibold text-dark">
                        {{ @$checkTurn['total_coefficient'] . '/' . @$checkTurn['coefficient_sum'] }}</p>
                </li>
                <li class="text-primary d-flex justify-content-between">
                    <p>Lượt đi làm muộn</p>
                    <p class="fw-semibold text-dark">{{ @$checkTurn['count_to_late'] }}</p>
                </li>
                <li class="text-primary d-flex justify-content-between">
                    <p>Số ngày nghỉ</p>
                    <p class="fw-semibold text-dark">{{ @$checkTurn['notWorking'] }}</p>
                </li>
                <li class="text-primary d-flex justify-content-between">
                    <p>Ngày nghỉ lễ</p>
                    <p class="fw-semibold text-dark">{{ count($day_off) }}</p>
                </li>

                @if ($checkTurn['hasTurn'])
                    @if ($checkTurn['created_by']['is_staff'])
                        <li class="text-primary d-flex justify-content-between">
                            <p>Người chốt</p>
                            <p class="fw-semibold text-dark">{{ $checkTurn['created_by']['data']['name'] }}</p>
                        </li>
                    @else
                        <li class="text-primary d-flex justify-content-between">
                            <p>Tài khoản chốt</p>
                            <p class="fw-semibold text-dark">{{ $checkTurn['created_by']['data']['name'] }}</p>
                        </li>
                    @endif
                    <li class="text-primary d-flex justify-content-between">
                        <p>Thời gian chốt</p>
                        <p class="fw-semibold text-dark">
                            {{ $checkTurn['created_by']['turn']['created_at']->format('H:i d-m-Y') }}
                        </p>
                    </li>
                @endif
            </ul>
        </div>

        @if (!$checkTurn['hasTurn'] && $create_turn_action)
            <center>
                <a onclick="show_turn_modal()" class="btn btn-sm btn-success" style="width: 40%;">
                    <i class="ri-quill-pen-line align-middle"> </i>
                    Chốt công
                </a>
            </center>
        @else
            @include('components.include.showCheckIn.function')
        @endif
    </div>
</div>