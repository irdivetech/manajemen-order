<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Only admin can update orders.
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
            'customer_name'  => ['sometimes', 'string', 'max:255'],
            'customer_phone' => ['sometimes', 'string', 'max:20'],
            'product_name'   => ['sometimes', 'string', 'max:255'],
            'product_type'   => ['sometimes', 'string', 'max:255'],
            'color'          => ['sometimes', 'string', 'max:100'],
            'size'           => ['sometimes', 'string', 'max:50'],
            'quantity'       => ['sometimes', 'integer', 'min:1'],
            'price'          => ['sometimes', 'numeric', 'min:0'],
            'order_date'     => ['sometimes', 'date'],
            'deadline'       => ['sometimes', 'date', 'after_or_equal:order_date'],
            'notes'          => ['nullable', 'string', 'max:2000'],
        ];
    }
}
