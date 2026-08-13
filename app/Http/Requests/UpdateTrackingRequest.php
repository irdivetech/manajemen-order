<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTrackingRequest extends FormRequest
{
    /**
     * Only admin can update tracking status.
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
        /** @var Order $order */
        $order = $this->route('order');
        $nextStatus = $order->getNextStatus();

        // Only allow the next sequential status
        $allowedStatuses = $nextStatus ? [$nextStatus] : [];

        return [
            'status'      => ['required', 'string', Rule::in($allowedStatuses)],
            'sub_type'    => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $status = $this->input('status');
            $targetStatusModel = \App\Models\MasterTrackingStatus::where('code', $status)->first();
            
            if ($targetStatusModel && $targetStatusModel->requires_payment) {
                /** @var Order $order */
                $order = $this->route('order');
                $order->loadMissing('invoice');
                
                if (!$order->invoice || !$order->invoice->isPaid()) {
                    $validator->errors()->add('status', "Pesanan harus lunas sebelum bisa lanjut ke tahap \"{$targetStatusModel->label}\".");
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var Order $order */
        $order = $this->route('order');
        $nextStatus = $order->getNextStatus();
        $nextLabel = $nextStatus ? Order::statusLabel($nextStatus) : 'Selesai';

        return [
            'status.in' => "Status hanya dapat dilanjutkan ke tahap berikutnya: \"{$nextLabel}\". Tidak bisa mundur atau melompati tahap.",
        ];
    }
}
