@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('content')
    @include('components.breadcrumb')



    <div class="card">
        <div class="card-body col-4">
            <form id="myForm" action="{{ route('user_updateFormAddcompanyToUser') }}" method="post">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div class="row" style="padding-left: 10%;">
                    <div style="margin-bottom: 15px">
                        <div class="input-group">
                            <input class="form-control" id="staff_card_id" type="text" placeholder="Nhập từ khóa"
                                onkeyup="findInPage()">
                            <i class="input-group-btn btn btn-success ri-search-2-line"></i>
                        </div>

                        <label class="form-label" id="resultCount"></label>
                        <div class="form-check form-switch form-switch-success" style="margin-bottom: 10px;">
                            <input class="form-check-input" type="checkbox" role="switch" id="checkBoxAll"
                                data-filters="color" color-filter onclick='checkBoxAllItem()'>
                            <label class="form-check-label"><b>Chọn tất cả</b></label>

                        </div>
                    </div>

                    <div class="card-body" style="padding: 0px !important;">
                        <div class="form-check form-switch" style="margin-bottom: 10px;">
                            @foreach ($companise as $company)
                                <div class="form-check form-switch mb-3">
                                    <input {{ in_array($company->id, $list_company_id) ? 'checked' : '' }}
                                        class="form-check-input check_box_items" type="checkbox" role="switch"
                                        data-filters="color" name='company[]' value="{{ $company->id }}" color-filter
                                        onclick='updateCheckAll()'>
                                    <label class="form-check-label" for="SwitchCheck1">{{ $company->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="float-right">
                    <a class="btn btn-danger" href="{{ route('user') }}">Quay lại</a>
                    <button type="submit" class="btn btn-primary" href="#">Lưu</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            updateCheckAll();
        });

        function checkBoxAllItem() {
            var checkboxAll = document.getElementById("checkBoxAll");
            var checkboxes = document.getElementsByClassName('check_box_items');

            if (checkboxAll.checked) {
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = true;
                    console.log(checkboxes[i]);
                }
            } else {
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = false;
                }
            }
        }

        function updateCheckAll() {
            var checkboxes = document.getElementsByClassName('check_box_items');
            var checkAll = document.getElementById("checkBoxAll");
            var allChecked = true;
            for (var i = 0; i < checkboxes.length; i++) {
                if (!checkboxes[i].checked) {
                    allChecked = false;
                    break;
                }
            }
            checkAll.checked = allChecked;
        }

        function findInPage() {
            // Lấy nội dung của tất cả các phần tử có lớp "form-check-label"
            const labels = document.querySelectorAll('.form-check-label');

            // Lấy từ cần tìm kiếm từ input
            const searchTerm = document.getElementById("staff_card_id").value;

            // Tạo biểu thức chính quy để tìm kiếm từ cần bôi đậm
            const regex = new RegExp(searchTerm, "gi");

            // Biến để lưu số lượng kết quả tìm thấy
            let matchCount = 0;

            // Duyệt qua từng phần tử "form-check-label" và bôi đậm các từ tìm thấy
            labels.forEach(label => {
                const originalText = label.textContent;
                const highlightedText = originalText.replace(regex, match => {
                    matchCount++; // Tăng số lượng kết quả khi tìm thấy từ
                    return `<span style="background-color: green; color: #fff">${match}</span>`;
                });
                label.innerHTML = highlightedText;
            });

            // Hiển thị số lượng kết quả tìm thấy
            const resultCountElement = document.getElementById("resultCount");
            if (searchTerm == '') {
                resultCountElement.textContent = `Vui lòng nhập từ khóa!`;
            } else {
                resultCountElement.textContent = `Tìm thấy: ${matchCount} kết quả`;
            }
            if (matchCount == 0 || searchTerm == '') {
                resultCountElement.style.color = "red";
            } else {
                resultCountElement.style.color = "green";
            }
        }
    </script>
@endsection
