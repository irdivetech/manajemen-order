<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentStatusRequest extends FormRequest
{
    /**
     * Only admin can update payment status.
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
            'payment_status' => [
                'required',
                'string',
                Rule::in([Invoice::PAYMENT_UNPAID, Invoice::PAYMENT_PAID, Invoice::PAYMENT_PARTIAL]),
            ],
            'payment_amount' => [
                'required_if:payment_status,partial,paid',
                'nullable',
                'numeric',
                'min:1'
            ],
            'payment_method' => [
                'nullable',
                'string',
                'max:100'
            ],
            'payment_notes' => [
                'nullable',
                'string',
                'max:500'
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_status.in' => 'Status pembayaran tidak valid.',
            'payment_amount.required_if' => 'Nominal pembayaran wajib diisi untuk status Lunas / Cicilan.',
            'payment_amount.min' => 'Nominal pembayaran tidak boleh kurang dari 1.',
        ];
    }
}
