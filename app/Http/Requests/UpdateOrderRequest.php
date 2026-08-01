<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'customer_name'     => ['sometimes', 'string', 'max:255'],
            'customer_phone'    => ['sometimes', 'string', 'max:20'],
            'customer_category' => ['sometimes', 'string', 'in:b2b,retail'],
            'customer_title'    => ['nullable', 'string', 'max:1000'],
            'customer_address'  => ['nullable', 'string', 'max:1000'],
            'product_name'      => ['sometimes', 'string', 'max:255'],
            'product_type'      => ['sometimes', 'string', 'max:100'],
            'color'             => ['sometimes', 'string', 'max:100'],
            'material'          => ['nullable', 'string', 'max:255'],
            'total_cost'        => ['sometimes', 'numeric', 'min:0'],
            'size_details'            => ['sometimes', 'array', 'min:1'],
            'size_details.*.gender'   => ['required_with:size_details', 'in:male,female,child'],
            'size_details.*.size'     => ['required_with:size_details', 'string', 'max:20'],
            'size_details.*.quantity' => ['required_with:size_details', 'integer', 'min:1'],
            'size_details.*.price'    => ['required_with:size_details', 'numeric', 'min:0'],
            'design_files'            => ['nullable', 'array', 'max:5'],
            'design_files.*'          => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'delete_design_files'     => ['nullable', 'array'],
            'delete_design_files.*'   => ['integer', 'exists:order_design_files,id'],
            'order_date'        => ['sometimes', 'date'],
            'deadline'          => ['sometimes', 'date', 'after_or_equal:order_date'],
            'notes'             => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $order = $this->route('order');
            
            // Get total cost: from input if provided, otherwise from existing order
            $totalCost = $this->has('total_cost') ? (float) $this->input('total_cost') : (float) $order->total_cost;
            
            // Calculate subtotal: from input size_details if provided, otherwise from existing order total_price
            if ($this->has('size_details')) {
                $subtotal = 0;
                foreach ($this->input('size_details', []) as $detail) {
                    if (isset($detail['quantity']) && isset($detail['price'])) {
                        $subtotal += ($detail['quantity'] * $detail['price']);
                    }
                }
            } else {
                $subtotal = (float) $order->total_price;
            }

            if ($totalCost > $subtotal) {
                $validator->errors()->add('total_cost', 'Total HPP / Modal Produksi tidak boleh melebihi Total Harga (Subtotal).');
            }
        });
    }
}
