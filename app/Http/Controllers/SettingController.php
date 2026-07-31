<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settingService)
    {
    }

    public function index()
    {
        $settings = $this->settingService->getSettings();
        return view(isMobile() ? 'settings.mobile.index' : 'settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'company_email' => 'required|email',
            'company_phone' => 'required|string|max:20',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        $this->settingService->updateSettings($validated);

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }
}
