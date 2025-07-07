<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Show the form for editing the application settings.
     */
    public function edit()
    {
        Gate::authorize('edit-settings');
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.edit', compact('settings'));
    }

    /**
     * Update the application settings in storage.
     */
    public function update(Request $request)
    {
        Gate::authorize('edit-settings');

        $validated = $request->validate([
            // Company Settings
            'company_name' => 'nullable|string|max:255',
            'company_address_line_1' => 'nullable|string|max:255',
            'company_address_line_2' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_website' => 'nullable|url|max:255',
            // Billing Parameters
            'invoice_prefix' => 'nullable|string|max:10',
            'invoice_start_number' => 'nullable|integer|min:1',
            'quotation_prefix' => 'nullable|string|max:10',
            'quotation_start_number' => 'nullable|integer|min:1',
            'default_payment_terms' => 'nullable|string|max:100',
            'default_due_days' => 'nullable|integer|min:0',
            // Mail Settings
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl,starttls',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        if ($request->hasFile('company_logo')) {
            // Get current logo path to delete it later
            $currentLogo = Setting::where('key', 'company_logo')->value('value');

            // Store new logo in the public disk
            $path = $request->file('company_logo')->store('logos', 'public');
            $validated['company_logo'] = $path;

            // Delete old logo if it exists
            if ($currentLogo && Storage::disk('public')->exists($currentLogo)) {
                Storage::disk('public')->delete($currentLogo);
            }
        }

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        // Clear the config cache to apply the new mail settings
        Artisan::call('config:clear');

        return redirect()->route('settings.edit')->with('success', 'Settings updated successfully.');
    }
}