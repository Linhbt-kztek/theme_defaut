<div class="card" style="height: 86vh;">
    <div class="card-header" style="padding-bottom:0px">
        <div class="row">
            <div class="col-6">
                <label for="">Nhập tên loại vé:</label>
                <div class="input-group">
                    <input class="form-control" id="searchText" name="searchText" placeholder="Nhập tên loại vé"
                        type="text" oninput="searchAndHide()" value="">
                </div>
            </div>
            <div class="col-6">
                <label for="">Chọn khu vực:</label>
                <select class="form-control" name="area_id" id="area_id_select" onchange="showTicketType()">
                    <option value="" selected="">--Chọn khu vực--</option>
                    @foreach ($areas as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <br>
            <b>Danh sách loại vé: <a id="count_ticket" class="text-danger"></a> loại</b>
        </div>
    </div>
    <div class="card-body scoll">
        <div class="row" id="list_ticket">
        </div>
    </div>
</div>
