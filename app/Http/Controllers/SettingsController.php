<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;


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
    
        $coreSettings = Setting::where('type', 'core')->get();
        $rules = [];
    
        foreach ($coreSettings as $setting) {
            if ($setting->key === 'company_logo') {
                $rules[$setting->key] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            } else {
                $rules[$setting->key] = 'nullable|string|max:255';
            }
        }
    
        $validated = $request->validate($rules);
    
        if ($request->hasFile('company_logo') && $request->file('company_logo')->isValid()) {
            $path = $request->file('company_logo')->store('logos', 'public');
            Setting::where('key', 'company_logo')->update(['value' => $path]);
        }
    
        foreach ($validated as $key => $value) {
            if ($key === 'company_logo') {
                continue;
            }
    
            if ($value !== null) {
                Setting::where('key', $key)->update(['value' => $value]);
            }
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