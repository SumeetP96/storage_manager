<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockTransferRequest extends FormRequest
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
            'date'              => 'required|date',
            'from_godown_id'    => 'different:to_godown_id|required|integer',
            'product_id'        => 'required|integer',
            'to_godown_id'      => 'different:from_godown_id|required|integer',
            'order_no'          => 'nullable|max:20',
            'invoice_no'        => 'nullable|max:20',
            'eway_bill_no'      => 'nullable|max:20',
            'transport.details' => 'nullable|max:100',
        ];
    }

    public function messages()
    {
        return [
            'from_godown_id.required'   => 'From account field is required.',
            'from_godown_id.different'  => 'Godowns must not be same.',
            'product_id.required'       => 'Product field is required.',
            'to_godown_id.required'     => 'To godown field is required.',
            'to_godown_id.different'    => 'Godowns must not be same.',
            'quantity.required'         => 'Quantity field is required.',
            'quantity.integer'          => 'Invalid quantity.',
        ];
    }
}
