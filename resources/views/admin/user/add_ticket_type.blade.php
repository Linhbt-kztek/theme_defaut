@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('content')
    @include('components.breadcrumb')
    @include('multiselect.script')
    <div class="row">
        <div class="col-6">
            @include('admin.user.include.list_ticket_type')
        </div>
        <div class="col-6">
            @include('admin.user.include.list_ticket_type_chose')
        </div>
    </div>
    <template id="html_tr_item">
        <tr id="chosed_item_[id]">
            <td class="text-center"></td>
            <td>[name]</td>
            <td style="text-align: right">[price]</td>
            <td class="text-center">
                <a class="btn-sm btn btn-outline-danger waves-effect waves-light" onclick="removeChosed('[id]')">
                    <i class="ri-delete-bin-line"></i>
                </a>
            </td>
            <input type="hidden" name="ticket_type_id[]" value="[id]">
        </tr>
    </template>
    @php
        $list_chosed = json_decode($user->list_ticket_type) ?? [];
    @endphp
    @include('admin.order.include.css')
    <script>
        let doomPageData = {
            ticket_type: @json($type_tickets),
            list_chosed: @json($list_chosed),
        }
    </script>
    <script src="{{ url('js/user/add_ticket_type.js') }}"></script>
@endsection
