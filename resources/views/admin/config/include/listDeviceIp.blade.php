<div class="card">
    <div class="card-header title_content">
        <b class="text-center">Danh sách máy bán hàng</b>
    </div>
    <div class="card-body border border-dashed border-start-0 border-end-0 row mb-3">
        <table class="table table-sm table-bordered align-middle table-nowrap mb-0 " id="tasksTable">
            <thead class="table-light text-muted">
                <tr class="text-center">
                    <th class="sort" style="width: 4%">STT</th>
                    <th class="sort">Tên máy</th>
                    <th class="sort">Địa chỉ IP</th>
                    <th class="sort">Địa chỉ IPv4</th>
                    <th class="sort">Địa chỉ IPv6</th>
                    <th style="width: 15%">Thao tác</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @isset($sale_devices)
                    @foreach ($sale_devices as $key => $item)
                        <tr id="tr_{{ $item['id'] }}">
                            {{--                                                        <td class="text-center"> <?php echo (session('search.page') - 1) * $limit + ($key + 1); ?></td> --}}
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->ip }}</td>
                            <td>{{ $item->ip_v4 }}</td>
                            <td>{{ $item->ip_v6 }}</td>
                            <td class="text-center">
                                <div class="flex-shrink-0">
                                    <ul class="list-inline tasks-list-menu mb-0">
                                        <li class="list-inline-item">
                                            <div title="Delete" id="{{ $item['id'] }}"
                                                class="btn-sm btn btn-outline-danger waves-effect waves-light delete_sale_device">
                                                <i class="ri-delete-bin-line"></i>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endisset
            </tbody>
        </table>

    </div>
</div>
<script>
    $('.delete_sale_device').click(function() {
        const id = $(this).attr('id')
        const data = {
            "_token": "{{ csrf_token() }}",
            "id": id
        }
        $.ajax({
            url: "{{ route('sale_device.destroySaleDevice') }}",
            type: 'post',
            data: data,
            success: function(result) {
                switch (result.status) {
                    case 200:
                        main_layout.alert_main(result.message)
                        location.reload()
                        break
                    case 401:
                        main_layout.alert_main(result.message, 'error')
                        location.reload()
                        break
                    default:
                        main_layout.alert_main('Xảy ra lỗi ra', 'error')
                        location.reload()
                        break
                }
            },
        })

    })
</script>
