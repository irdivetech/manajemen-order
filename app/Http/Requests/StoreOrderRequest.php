<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Only admin can create orders.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'customer_name'        => ['required', 'string', 'max:255'],
            'customer_phone'       => ['required', 'string', 'max:20'],
            'customer_category'    => ['required', 'string', 'in:b2b,retail'],
            'customer_title'       => ['nullable', 'string', 'max:1000'],
            'customer_address'     => ['nullable', 'string', 'max:1000'],
            'customer_city'        => ['nullable', 'string', 'max:255'],
            'customer_district'    => ['nullable', 'string', 'max:255'],
            
            'clothing_category_id' => ['required', 'exists:master_clothing_categories,id'],
            'product_name'         => ['required', 'string', 'max:255'],
            'product_type'         => ['required', 'string', 'max:100'],
            'model_product'        => ['nullable', 'string', 'max:255'],
            'has_embroidery'       => ['boolean'],
            'material_id'          => ['required', 'exists:master_materials,id'],
            
            'total_cost'           => ['required', 'numeric', 'min:0'],
            'size_details'         => ['required', 'array', 'min:1'],
            'size_details.*.color' => ['required', 'string', 'max:100'],
            'size_details.*.gender_id'        => ['required', 'exists:master_genders,id'],
            'size_details.*.size_type'        => ['required', 'in:standard,big'],
            'size_details.*.size_id'          => ['required', 'exists:master_sizes,id'],
            'size_details.*.quantity'         => ['required', 'integer', 'min:1'],
            'size_details.*.price'            => ['required', 'numeric', 'min:0'],
            
            'design_files'         => ['nullable', 'array', 'max:5'],
            'design_files.*'       => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'order_date'           => ['required', 'date'],
            'deadline'             => ['required', 'date', 'after_or_equal:order_date'],
            'notes'                => ['nullable', 'string', 'max:2000'],
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
            $totalCost = (float) $this->input('total_cost', 0);
            $sizeDetails = $this->input('size_details', []);
            
            $subtotal = 0;
            foreach ($sizeDetails as $detail) {
                if (isset($detail['quantity']) && isset($detail['price'])) {
                    $subtotal += ($detail['quantity'] * $detail['price']);
                }
            }

            if ($totalCost > $subtotal) {
                $validator->errors()->add('total_cost', 'Total HPP / Modal Produksi tidak boleh melebihi Total Harga (Subtotal).');
            }
        });
    }
}
