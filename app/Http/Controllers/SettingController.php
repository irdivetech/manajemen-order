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
        $bankAccounts = \App\Models\BankAccount::all();
        return view(isMobile() ? 'settings.mobile.index' : 'settings.index', compact('settings', 'bankAccounts'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'company_email' => 'required|email',
            'company_phone' => 'required|string|max:20',
            'company_wa' => 'nullable|string|max:50',
            'company_ig' => 'nullable|string|max:100',
            'company_tiktok' => 'nullable|string|max:100',
            'owner_name' => 'nullable|string|max:255',
            'owner_title' => 'nullable|string|max:255',
            'signature_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'company_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'resi_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $settings = $this->settingService->getSettings();

        // Handle signature upload
        if ($request->hasFile('signature_image')) {
            if (!empty($settings['signature_image'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($settings['signature_image']);
            }
            $validated['signature_image'] = $request->file('signature_image')->store('settings', 'public');
        } else {
            $validated['signature_image'] = $settings['signature_image'] ?? '';
        }

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            if (!empty($settings['company_logo'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($settings['company_logo']);
            }
            $validated['company_logo'] = $request->file('company_logo')->store('settings', 'public');
        } else {
            $validated['company_logo'] = $settings['company_logo'] ?? '';
        }

        // Handle resi logo upload
        if ($request->hasFile('resi_logo')) {
            if (!empty($settings['resi_logo'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($settings['resi_logo']);
            }
            $validated['resi_logo'] = $request->file('resi_logo')->store('settings', 'public');
        } else {
            $validated['resi_logo'] = $settings['resi_logo'] ?? '';
        }

        $this->settingService->updateSettings($validated);

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }
}
