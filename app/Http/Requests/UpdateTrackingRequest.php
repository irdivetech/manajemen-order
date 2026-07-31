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
            'description' => ['required', 'string', 'max:1000'],
        ];
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
