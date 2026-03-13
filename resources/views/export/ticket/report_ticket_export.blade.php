@php $col=10; @endphp
<table class="table-bordered table-responsive">
    <thead>

        <tr>
            <td colspan="{{ $col }}" align="left" valign="middle" style="font-size:12px; text-align: center">
                <p><b>{{ $config_login['title_web'] ?? config('software.title') }}</b></p>
            </td>
        </tr>

        <tr>
            <td colspan="{{ $col }}" align="center" valign="middle" style="font-size:16px"><b>BÁO CÁO TỔNG HỢP
                    VÉ</b>
            </td>
        </tr>

        <tr>
            <td colspan="{{ $col }}" align="center" valign="middle"><i>Ngày báo cáo :
                    {{ date('G:i d-m-Y') }}</i></td>
        </tr>


        <tr>
            <td colspan="{{ $col }}" align="center" valign="middle">Báo cáo kết suất dữ liệu
                {{ session('ticket_search.date') }}</td>
        </tr>

        <tr>
            {{-- <td colspan="{{ $col }}" align="center" valign="middle">{{ session('ticket_search.date') }}</td> --}}
            {{-- <td ></td>
      
        <td style="text-align: center;vertical-align: middle"><b>Mã vé/mã hoá đơn</b></td>
        <td style="text-align: left;vertical-align: middle">{{session('search.key_search')}}</td>
        <td style="text-align: center;vertical-align: middle"><b>Hình thức mua</b></td>
        <td style="text-align: center;vertical-align: middle">{{session('search.type_name')}}</td>
        <td style="text-align: center;vertical-align: middle"><b>Ngày bán</b></td>
        <td style="text-align: center;vertical-align: middle"><b>từ</b></td>
        <td style="text-align: center;vertical-align: middle">{{session('search.start_date')}}</td>
        <td ></td> --}}
        </tr>
        <tr>
            {{-- <td></td>
        <td></td>
        <td style="text-align: center;vertical-align: middle"><b>Đối tượng KH</b></td>
        <td style="text-align: left;vertical-align: middle">{{session('search.customer_classification_name')}}</td>
        <td style="text-align: center;vertical-align: middle"><b>Hình thức TT</b></td>
        <td style="text-align: center;vertical-align: middle">{{session('search.payment_methods_name')}}</td>
        <td></td>
        <td style="text-align: center;vertical-align: middle"><b>đến</b></td>
        <td style="text-align: center;vertical-align: middle">{{session('search.end_date ')}}</td>
        <td></td> --}}
        </tr>
        <tr>
            {{-- <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align: center;vertical-align: middle"><b>Trạng  thái</b></td>
        <td style="text-align: center;vertical-align: middle">{{session('search.status_name')}}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td> --}}
        </tr>

        <tr>
            <td colspan="{{ $col }}" align="left" valign="middle"></td>
        </tr>



        <tr>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">STT
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã vé
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã hoá đơn
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tên loại vé
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Số seri</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Mã biên lai</td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Ngày bán
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Ngày sử dụng
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Hạn sử dụng
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Giá vé(đ)
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Trạng thái
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #E2EFD9; vertical-align: middle ">Tình trạng
            </td>
        </tr>

    </thead>
    <tbody>
        @foreach ($data as $key => $ticket)
            <tr>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $key + 1 }}</td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">{{ $ticket->code }} </td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    {{ $ticket->order->code_order ?? '' }}</td>
                <td style="white-space: normal; vertical-align: middle;">{{ $ticket->ticket_type_name }}</td>
                <td class="text-center">{{ $ticket->bill->seri_code ?? '' }}</td>
                <td class="text-center">{{ $ticket->bill->reservation_code ?? '' }}</td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    {{ !empty($ticket->created_at) ? \Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y') : '' }}
                </td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    @php
                        $date = '';
                        if (count($ticket->events) > 0) {
                            $date = $ticket->events[0]->created_at;
                        } else {
                            $date = $ticket->use_date;
                        }
                    @endphp
                    {{ !empty($date) ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '' }}
                </td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    {{ !empty($ticket->expire_date) ? \Carbon\Carbon::parse($ticket->expire_date)->format('d/m/Y') : '' }}
                </td>
                <td style="text-align: right; white-space: normal; vertical-align: middle;">
                    {{ number_format($ticket->price ?? '', 0, '.', '.') }}</td>
                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    {{ $ticket->status == 1 ? 'Hoạt động' : 'Khoá' }}</td>

                @php
                    if (!empty($ticket->expire_date)) {
                        $expireDate = \Carbon\Carbon::parse($ticket->expire_date);
                        $has_expire_date = true;
                        $expire_date = $expireDate->isPast();
                    } else {
                        $has_expire_date = false;
                        $expire_date = false;
                    }
                @endphp

                <td style="text-align: center; white-space: normal; vertical-align: middle;">
                    {{ count($ticket->events) > 0 ? 'Đã sử dụng' : ($has_expire_date ? ($expire_date ? 'Hết hạn' : 'Chưa sử dụng') : 'Chưa sử dụng') }}
                </td>
            </tr>
        @endforeach

    </tbody>
</table>
