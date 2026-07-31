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
            'customer_name'     => ['required', 'string', 'max:255'],
            'customer_phone'    => ['required', 'string', 'max:20'],
            'customer_category' => ['required', 'string', 'in:b2b,retail'],
            'customer_title'    => ['nullable', 'string', 'max:1000'],
            'customer_address'  => ['nullable', 'string', 'max:1000'],
            'product_name'      => ['required', 'string', 'max:255'],
            'product_type'      => ['required', 'string', 'max:100'],
            'color'             => ['required', 'string', 'max:100'],
            'material'          => ['nullable', 'string', 'max:255'],
            'total_cost'        => ['required', 'numeric', 'min:0'],
            'size_details'            => ['required', 'array', 'min:1'],
            'size_details.*.gender'   => ['required', 'in:male,female,child'],
            'size_details.*.size'     => ['required', 'string', 'max:20'],
            'size_details.*.quantity' => ['required', 'integer', 'min:1'],
            'size_details.*.price'    => ['required', 'numeric', 'min:0'],
            'design_files'            => ['nullable', 'array', 'max:5'],
            'design_files.*'          => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'order_date'        => ['required', 'date'],
            'deadline'          => ['required', 'date', 'after_or_equal:order_date'],
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
}
