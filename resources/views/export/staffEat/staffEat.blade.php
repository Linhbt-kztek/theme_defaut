<table class="table-bordered table-responsive">
    <thead>
        <tr>
            <td colspan="12" align="left" valign="middle" style="font-size: 12px; text-align: center">
                <p><b>LOTTE - BẾP ĂN</b></p>
            </td>
        </tr>
        <tr>
            <td colspan="12" align="center" valign="middle" style="font-size: 16px">
                <b>LỊCH SỬ ĂN</b>
            </td>
        </tr>
        <tr>
            <td colspan="12" align="center" valign="middle"><i>Ngày xuất báo cáo : {{ $today }}</i></td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="12" align="left" valign="middle"></td>
        </tr>

        <tr>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                STT
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                Tên công ty
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                Mã thẻ
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                Mã nhân viên
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                Tên nhân viên
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                Mã suất ăn
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                Tên suất ăn
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                Đơn giá
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                vị trí ăn
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                Ngày ăn
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                Giờ ăn
            </td>
            <td align="center" valign="middle"
                style="text-align: center; background-color: #e2efd11; vertical-align: middle">
                Bữa ăn
            </td>
        </tr>
    </thead>
    <tbody style="border: 1px solid">
        @if (!empty($data))
            <?php $count = 0; ?>
            @foreach ($data as $key => $val)
                <?php $count++; ?>
                <tr>
                    <td style="text-align: center; white-space: normal; vertical-align: top">{{ $count }}</td>
                    <td style=" @if(@$val->staff->type == 1) color: red; @endif text-align: center; white-space: normal; vertical-align: top">
                        {{ @$val->staff->company->name }}
                    </td>

                    <td class="sort" style="text-align: center; vertical-align: top">{{ @$val->staff->card_id }}</td>

                    <td class="sort" style="text-align: center; vertical-align: top">{{ @$val->staff->code }}</td>

                    <td class="sort" style="text-align: left; vertical-align: top">{{ @$val->staff->name }}</td>

                    <td class="sort" style="text-align: center; vertical-align: top">{{ @$val->eat->code }}</td>
                    <td class="sort" style="text-align: center; vertical-align: top">{{ @$val->eat->name }}</td>
                    <td class="sort" style="text-align: center; vertical-align: top">{{ @$val->eat->price }}
                    </td>
                    <td class="sort" style="text-align: center; vertical-align: top">
                        {{ !empty($val->Location->name) ? $val->Location->name : '-' }}</td>
                    <td class="sort" style="text-align: center; vertical-align: top">
                        {{ date('d-m-Y', strtotime(@$val->created_at)) }}</td>
                    <td class="sort" style="text-align: center; vertical-align: top">
                        {{ date('H:i:s', strtotime(@$val->created_at)) }}</td>
                    <td class="sort" style="text-align: center; vertical-align: top">{{ @$val->getTimeEat() }}
                    </td>

                </tr>
            @endforeach
        @endif
    </tbody>
</table>
