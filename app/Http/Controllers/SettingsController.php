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
        $coreSettings = Setting::where('type', 'core')->get();
        $customSettings = Setting::where('type', 'custom')->get();
        return view('settings.edit', compact('coreSettings', 'customSettings'));
    }

    /**
     * Update the application settings in storage.
     */
    public function update(Request $request)
    {
        Gate::authorize('edit-settings');
        // Validar solo los core settings
        $coreKeys = Setting::where('type', 'core')->pluck('key');
        $rules = [];
        foreach ($coreKeys as $key) {
            $rules[$key] = 'nullable|string|max:255';
        }
        $validated = $request->validate($rules);
        foreach ($validated as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }
        return redirect()->route('settings.edit')->with('success', __('settings.Updated successfully'));
    }

    public function storeCustom(Request $request)
    {
        Gate::authorize('edit-settings');
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'nullable|string',
        ]);
        Setting::create([
            'key' => $validated['key'],
            'value' => $validated['value'],
            'type' => 'custom',
            'is_editable' => true,
        ]);
        return redirect()->route('settings.edit')->with('success', __('settings.Custom setting added'));
    }

    public function destroyCustom(Setting $setting)
    {
        Gate::authorize('edit-settings');
        if ($setting->type === 'custom' && $setting->is_editable) {
            $setting->delete();
            return redirect()->route('settings.edit')->with('success', __('settings.Custom setting deleted'));
        }
        return redirect()->route('settings.edit')->with('error', __('settings.Cannot delete core setting'));
    }
}