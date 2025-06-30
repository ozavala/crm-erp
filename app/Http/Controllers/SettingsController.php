<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Show the form for editing the application settings.
     */
    public function edit()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.edit', compact('settings'));
    }

    /**
     * Update the application settings in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_address_line_1' => 'nullable|string|max:255',
            'company_address_line_2' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
                ['value' => $value]
            );
        }

        return redirect()->route('settings.edit')->with('success', 'Settings updated successfully.');
    }
}