<style>
    .mt-4 {
        margin-top: 0.6rem !important;
    }
</style>
@can('create_invoice')
    <form id="form_invoice_info" action="{{ route('config.updateInvoice') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Cấu hình hóa đơn điện tử và vé điện tử</h3>
            </div>

            <div class="card-body border border-dashed border-start-0 border-end-0 row">
                <div class="container">
                    <div class="row">
                        <div class="col-6">
                            <div class="form_input">
                                <label class="form-label">Tên công ty<span style="color: red;font-weight: bold">*</span>
                                </label>
                                <input class="form-control" name="companyName" type="text"
                                    value="{{ old('companyName', isset($data['companyName']) ? $data['companyName'] : '') }}"
                                    placeholder="">
                                @if ($errors->has('companyName'))
                                    <div class="bg-danger text-white text-center py-1">
                                        <span>{{ $errors->first('companyName') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="form_input">
                                <label class="form-label">Mã số thuế <span style="color: red;font-weight: bold">*</span>
                                </label>
                                <input class="form-control" name="taxCode" type="text"
                                    value="{{ old('taxCode', isset($data['taxCode']) ? $data['taxCode'] : '') }}"
                                    placeholder="">
                                @if ($errors->has('taxCode'))
                                    <div class="bg-danger text-white text-center py-1">
                                        <span>{{ $errors->first('taxCode') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-check form-switch" style="padding-top: 25px;">
                                <input type="checkbox" class="form-check-input" value="1" name="use_receipt"
                                    id="use_receipt" onclick="useReceipt()"
                                    {{ !empty($data['use_receipt']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="use_receipt">Sử dụng biên lai</label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row" id="info_invoice">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-md-12 provider_div">
                                    <div class="border pt-0 p-3 mb-3" style="padding-top:0px !important;">
                                        <h4 class="mt-4">Đơn vị cung cấp dịch vụ:</h4>
                                        <div class="form-group">
                                            <label for="taxRateTicket">Tên <span
                                                    style="color: red;font-weight: bold">*</span></label></label>
                                            <select id="providerInput" name="content[invoiceConfiguration][provider]"
                                                onchange="choseProvider()" class="form-control">
                                                <option value="" disabled>Chọn nhà cung cấp</option>
                                                <option value="MISA"
                                                    {{ old('content.invoiceConfiguration.provider', $data['invoiceConfiguration']['provider'] ?? '') == 'MISA' ? 'selected' : '' }}>
                                                    MISA
                                                </option>
                                                <option value="VNPT"
                                                    {{ old('content.invoiceConfiguration.provider', $data['invoiceConfiguration']['provider'] ?? '') == 'VNPT' ? 'selected' : '' }}>
                                                    VNPT
                                                </option>
                                            </select>
                                            @if ($errors->has('content.invoiceConfiguration.provider'))
                                                <div class="bg-danger text-white text-center py-1">
                                                    {{ $errors->first('content.invoiceConfiguration.provider') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="border pt-0 p-3 mb-3">
                                        <h4 class="mt-4">Vé điện tử</h4>
                                        <div class="form-group">
                                            <label for="ticketTaxRate">Mức thuế suất (%):<span
                                                    style="color: red;font-weight: bold">*</span></label>
                                            <input type="text" min="0" id="ticketTaxRate"
                                                name="content[invoiceConfiguration][ticket][taxRate]"
                                                value="{{ old('content.invoiceConfiguration.ticket.taxRate', $data['invoiceConfiguration']['ticket']['taxRate'] ?? '') }}"
                                                class="form-control">
                                            @if ($errors->has('content.invoiceConfiguration.ticket.taxRate'))
                                                <div class="bg-danger text-white text-center py-1">
                                                    {{ $errors->first('content.invoiceConfiguration.ticket.taxRate') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border p-3 pt-0">
                                <h4 class="mt-4">Hóa đơn điện tử</h4>
                                <div class="form-group">
                                    <label for="templateCode">Mã mẫu hoá đơn:<span
                                            style="color: red;font-weight: bold">*</span></label>
                                    <input type="text" id="templateCode"
                                        name="content[invoiceConfiguration][bill][templateCode]"
                                        value="{{ old('content.invoiceConfiguration.bill.templateCode', !empty($data['invoiceConfiguration']['bill']['templateCode']) ? $data['invoiceConfiguration']['bill']['templateCode'] : '') }}"
                                        class="form-control">
                                    @if ($errors->has('content.invoiceConfiguration.bill.templateCode'))
                                        <div class="bg-danger text-white text-center py-1">
                                            {{ $errors->first('content.invoiceConfiguration.bill.templateCode') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="symbolCode" style="margin-top: .5rem;">Ký hiệu của hoá đơn:<span
                                            style="color: red;font-weight: bold">*</span></label>
                                    <input type="text" id="symbolCode"
                                        name="content[invoiceConfiguration][bill][symbolCode]"
                                        value="{{ old('content.invoiceConfiguration.bill.symbolCode', !empty($data['invoiceConfiguration']['bill']['symbolCode']) ? $data['invoiceConfiguration']['bill']['symbolCode'] : '') }}"
                                        class="form-control">
                                    @if ($errors->has('content.invoiceConfiguration.bill.symbolCode'))
                                        <div class="bg-danger text-white text-center py-1">
                                            {{ $errors->first('content.invoiceConfiguration.bill.symbolCode') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="invoiceTypeCode" style="margin-top: .5rem;">Mã loại hoá đơn:<span
                                            style="color: red;font-weight: bold">*</span></label>
                                    <input type="text" id="invoiceTypeCode"
                                        name="content[invoiceConfiguration][bill][invoiceTypeCode]"
                                        value="{{ old('content.invoiceConfiguration.bill.invoiceTypeCode', !empty($data['invoiceConfiguration']['bill']['invoiceTypeCode']) ? $data['invoiceConfiguration']['bill']['invoiceTypeCode'] : '') }}"
                                        class="form-control">
                                    @if ($errors->has('content.invoiceConfiguration.bill.invoiceTypeCode'))
                                        <div class="bg-danger text-white text-center py-1">
                                            {{ $errors->first('content.invoiceConfiguration.bill.invoiceTypeCode') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="taxRate" style="margin-top: .5rem;">Mức thuế suất (%):<span
                                            style="color: red;font-weight: bold">*</span></label>
                                    <input type="text" min="0" id="taxRate"
                                        name="content[invoiceConfiguration][bill][taxRate]"
                                        value="{{ old('content.invoiceConfiguration.bill.taxRate', !empty($data['invoiceConfiguration']['bill']['taxRate']) ? $data['invoiceConfiguration']['bill']['taxRate'] : '') }}"
                                        class="form-control">
                                    @if ($errors->has('content.invoiceConfiguration.bill.taxRate'))
                                        <div class="bg-danger text-white text-center py-1">
                                            {{ $errors->first('content.invoiceConfiguration.bill.taxRate') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer">
                <div class="col-sm-auto">
                    <button class="btn btn-primary" type="submit">
                        Cập nhật
                    </button>
                </div>
            </div>
        </div>
    </form>
@endcan
<script>
    $(document).ready(function() {
        useReceipt();
        choseProvider();
    });

    function useReceipt() {
        if ($('#use_receipt').is(':checked')) {
            $('#info_invoice').addClass('d-none');
        } else {
            $('#info_invoice').removeClass('d-none');
        }
    }

    function choseProvider() {
        provider = $('#providerInput').val();
        if (provider == 'VNPT') {
            $('.col-md-12').addClass('d-none');
            $('.col-md-6').addClass('d-none');
            $('.provider_div').removeClass('d-none');
        } else {
            $('.col-md-6').removeClass('d-none');
            $('.col-md-12').removeClass('d-none');
        }

    }
</script>
