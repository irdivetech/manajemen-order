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
            'customer_province'    => ['nullable', 'string', 'max:255'],
            'customer_city'        => ['nullable', 'string', 'max:255'],
            'customer_district'    => ['nullable', 'string', 'max:255'],
            
            'clothing_category_id' => ['sometimes', 'exists:master_clothing_categories,id'],
            'product_name'      => ['sometimes', 'string', 'max:255'],
            'product_type'      => ['sometimes', 'string', 'max:100'],
            'model_product'        => ['nullable', 'string', 'max:255'],
            'has_embroidery'       => ['boolean'],
            'material_id'          => ['sometimes', 'exists:master_materials,id'],
            
            'total_cost'        => ['sometimes', 'numeric', 'min:0'],
            'size_details'            => ['sometimes', 'array', 'min:1'],
            'size_details.*.color' => ['required_with:size_details', 'string', 'max:100'],
            'size_details.*.gender_id'        => ['required_with:size_details', 'exists:master_genders,id'],
            'size_details.*.size_type'        => ['required_with:size_details', 'in:standard,big'],
            'size_details.*.size_id'          => ['required_with:size_details', 'exists:master_sizes,id'],
            'size_details.*.quantity'         => ['required_with:size_details', 'integer', 'min:1'],
            'size_details.*.price'            => ['required_with:size_details', 'numeric', 'min:0'],
            
            'design_files'            => ['nullable', 'array', 'max:5'],
            'design_files.*'          => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'delete_design_files'     => ['nullable', 'array'],
            'delete_design_files.*'   => ['integer', 'exists:order_design_files,id'],
            'order_date'        => ['sometimes', 'date'],
            'deadline'          => ['sometimes', 'date', 'after_or_equal:order_date'],
            'notes'             => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'deadline.after_or_equal' => 'The deadline must be on or after the order date.',
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
