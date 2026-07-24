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
            'customer_name'  => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'product_name'   => ['required', 'string', 'max:255'],
            'product_type'   => ['required', 'string', 'max:255'],
            'color'          => ['required', 'string', 'max:100'],
            'size'           => ['required', 'string', 'max:50'],
            'quantity'       => ['required', 'integer', 'min:1'],
            'price'          => ['required', 'numeric', 'min:0'],
            'order_date'     => ['required', 'date'],
            'deadline'       => ['required', 'date', 'after_or_equal:order_date'],
            'notes'          => ['nullable', 'string', 'max:2000'],
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
