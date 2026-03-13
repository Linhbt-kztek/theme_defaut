<?php

namespace App\Http\Requests\Config;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Nhà cung cấp: string và độ dài tối đa 100 ký tự
            'content.invoiceConfiguration.provider' => 'required|string|max:100',

            // Cấu hình hóa đơn điện tử
            // 'content.invoiceConfiguration.bill.templateCode' => 'required|string|max:100',
            // 'content.invoiceConfiguration.bill.symbolCode' => 'required|string|max:100',
            // 'content.invoiceConfiguration.bill.invoiceTypeCode' => 'required|string|max:100',
            // 'content.invoiceConfiguration.bill.taxRate' => 'required|numeric|min:0|max:100',

            // Cấu hình vé điện tử
          //  'content.invoiceConfiguration.ticket.type' => 'required|string|max:100',
            // 'content.invoiceConfiguration.ticket.taxRate' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'content.invoiceConfiguration.provider.required' => 'Nhà cung cấp là bắt buộc.',
            'content.invoiceConfiguration.provider.string' => 'Nhà cung cấp phải là một chuỗi.',
            'content.invoiceConfiguration.provider.max' => 'Nhà cung cấp không được vượt quá 100 ký tự.',

            'content.invoiceConfiguration.bill.templateCode.required' => 'Template Code là bắt buộc.',
            'content.invoiceConfiguration.bill.templateCode.string' => 'Template Code phải là một chuỗi.',
            'content.invoiceConfiguration.bill.templateCode.max' => 'Template Code không được vượt quá 100 ký tự.',

            'content.invoiceConfiguration.bill.symbolCode.required' => 'Invoice Type Code là bắt buộc.',
            'content.invoiceConfiguration.bill.symbolCode.string' => 'Invoice Type Code phải là một chuỗi.',
            'content.invoiceConfiguration.bill.symbolCode.max' => 'Invoice Type Code không được vượt quá 100 ký tự.',

            'content.invoiceConfiguration.bill.invoiceTypeCode.required' => 'Type là bắt buộc.',
            'content.invoiceConfiguration.bill.invoiceTypeCode.string' => 'Type phải là một chuỗi.',
            'content.invoiceConfiguration.bill.invoiceTypeCode.max' => 'Type không được vượt quá 100 ký tự.',

            'content.invoiceConfiguration.bill.taxRate.required' => 'Thuế suất là bắt buộc.',
            'content.invoiceConfiguration.bill.taxRate.numeric' => 'Thuế suất phải là số.',
            'content.invoiceConfiguration.bill.taxRate.min' => 'Thuế suất không được nhỏ hơn :0',
            'content.invoiceConfiguration.bill.taxRate.max' => 'Thuế suất không được lớn hơn :100',

            'content.invoiceConfiguration.ticket.taxRate.required' => 'Thuế suất là bắt buộc.',
            'content.invoiceConfiguration.ticket.taxRate.numeric' => 'Thuế suất phải là số.',
            'content.invoiceConfiguration.ticket.taxRate.min' => 'Thuế suất không được nhỏ hơn :0',
            'content.invoiceConfiguration.ticket.taxRate.max' => 'Thuế suất không được lớn hơn :100',
        ];
    }
}
