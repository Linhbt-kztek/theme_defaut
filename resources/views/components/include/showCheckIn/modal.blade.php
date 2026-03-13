@if ($create_turn_action)
    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createTurnForm" action="{{ route('turn.store') }}" method="post" aria-multiline="true">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">Thông tin chốt công</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <a>Nhân viên: <b>{{ @$staff->name }}</b></a><br>
                                <a>Mã nhân viên: <b>{{ @$staff->code }}</b></a><br>
                                <a>Chi nhánh: <b>{{ @$staff->branch->name }}</b></a><br>
                                <a>Phòng: <b>{{ @$staff->department->name }}</b></a>
                            </div>
                            <div class="col-6">
                                <a><b>{{ $checkTurn['name_month'] }}</b></a><br>
                                <a>Công đủ:
                                    <b>{{ @$checkTurn['total_coefficient'] . '/' . @$checkTurn['coefficient_sum'] }}</b></a><br>
                                <a>Số ngày nghỉ: <b>{{ @$checkTurn['notWorking'] }}</b></a><br>
                                <a>Lượt đi muộn: <b>{{ @$checkTurn['count_to_late'] }}</b></a>
                            </div>
                        </div>
                        <div style="margin-top: 12px">
                            <label>Nhập ghi chú (nếu có):</label>
                            <textarea class="form-control" name="note" id="" cols="30" rows="5"
                                      placeholder="Nhập thông tin ghi chú ..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <i class="text-danger"><b>Lưu ý:</b></i>
                            </div>
                            <div class="col-10">
                                <i class="text-danger">
                                    <a>Vui lòng kiểm tra thông tin kỹ trước khi xác nhận!</a><br>
                                    <a> Bạn sẽ không thể chốt lại sau khi nhấn xác nhận</a>
                                </i>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i
                                class="ri-close-line align-bottom me-1"></i>Đóng</button>
                        <button type="button" onclick="submitCreateTurn()" class="btn btn-success"><i
                                class="ri-check-double-line align-bottom me-1"></i>
                            Xác nhận chốt công</button>
                    </div>
                    <input type="hidden" name="month" value="{{ @$data['month'] }}">
                    <input type="hidden" name="staff_id" value="{{ @$staff->id }}">
                </form>
            </div>
        </div>
    </div>
@endif

