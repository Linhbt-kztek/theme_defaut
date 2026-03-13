<form id="my_form" action="{{ route('user_saveFormAddTicketTypeToUser', $user->id) }}" method="post"
    aria-multiline="true" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-header">
            <h4>Danh sách loại vé đã chọn</h4>
        </div>
        <div class="card-body scoll" style="max-height: 70vh">
            <div class="table-responsive">
                <table class="table align-middle table-nowrap mb-0">
                    <thead>
                        <tr class="text-center">
                            <th scope="col">STT</th>
                            <th scope="col">Tên loại vé</th>
                            <th scope="col">Giá bán</th>
                            <th scope="col">Tháo tác</th>
                        </tr>
                    </thead>
                    <tbody id="chosed_item">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div style="float: right">
                <a class="btn btn-danger" href="{{ route('user') }}">
                    <i class="ri-arrow-go-back-line align-bottom me-1"></i>Quay lại
                </a>
                <button class="btn btn-primary" type="submit">
                    <i class="ri-save-line align-bottom me-1"></i>Lưu dữ liệu
                </button>
            </div>
        </div>
    </div>
</form>
