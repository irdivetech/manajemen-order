<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class SettingService
{
    private string $file = 'settings.json';

    public function getSettings(): array
    {
        if (!Storage::disk('local')->exists($this->file)) {
            $this->updateSettings($this->defaultSettings());
            return $this->defaultSettings();
        }
        return json_decode(Storage::disk('local')->get($this->file), true) ?? $this->defaultSettings();
    }

    public function updateSettings(array $data): void
    {
        Storage::disk('local')->put($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function get(string $key)
    {
        return $this->getSettings()[$key] ?? null;
    }

    private function defaultSettings(): array
    {
        return [
            'company_name' => 'POMS Garment',
            'company_address' => 'Jalan Produksi No. 123, Kota Anda, Provinsi 12345',
            'company_email' => 'kontak@poms.com',
            'company_phone' => '08123456789',
            'company_logo' => '', // Path to company logo image
            'owner_name' => '', // e.g. Rini Eka Maulani
            'owner_title' => '', // e.g. Owner Shaleea
            'signature_image' => '', // Path to signature image
        ];
    }
}
