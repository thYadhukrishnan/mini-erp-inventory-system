<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id'              => ['required', 'integer', 'exists:suppliers,id'],
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.product_id'       => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'         => ['required', 'integer', 'min:1'],
            'items.*.unit_price'       => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'                => 'At least one line item is required.',
            'items.*.product_id.exists'     => 'One or more selected products do not exist.',
            'items.*.quantity.min'          => 'Quantity must be at least 1.',
            'items.*.unit_price.min'        => 'Unit price must be greater than 0.',
        ];
    }
}
