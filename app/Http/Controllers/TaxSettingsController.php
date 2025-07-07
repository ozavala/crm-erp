<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Helpers\TaxRateDefaults;

class TaxSettingsController extends Controller
{
    /**
     * Display the tax settings page.
     */
    public function index(): View
    {
        $countries = [
            'EC' => 'Ecuador',
            'ES' => 'España',
            'MX' => 'México',
        ];
        
        $defaultCountry = Setting::where('key', 'default_country_tax')->first()?->value ?? 'EC';
        
        $taxSettings = [];
        foreach ($countries as $code => $name) {
            $setting = Setting::where('key', "tax_rates_{$code}")->first();
            $taxSettings[$code] = [
                'name' => $name,
                'rates' => $setting ? json_decode($setting->value, true) : [],
                'is_default' => $code === $defaultCountry,
            ];
        }
        
        $serviceSettings = [
            'tax_includes_services' => Setting::where('key', 'tax_includes_services')->first()?->value ?? 'true',
            'tax_includes_transport' => Setting::where('key', 'tax_includes_transport')->first()?->value ?? 'false',
        ];
        
        return view('tax_settings.index', compact('taxSettings', 'countries', 'defaultCountry', 'serviceSettings'));
    }
    
    /**
     * Update tax rates for a specific country.
     */
    public function updateCountryRates(Request $request, string $countryCode)
    {
        $request->validate([
            'rates' => 'required|array',
            'rates.*.name' => 'required|string',
            'rates.*.rate' => 'required|numeric|min:0|max:100',
            'rates.*.description' => 'nullable|string',
        ]);
        
        $setting = Setting::where('key', "tax_rates_{$countryCode}")->first();
        if (!$setting) {
            Setting::create([
                'key' => "tax_rates_{$countryCode}",
                'value' => json_encode($request->rates),
                'type' => 'json',
            ]);
        } else {
            $setting->update(['value' => json_encode($request->rates)]);
        }
        
        return redirect()->route('tax-settings.index')
            ->with('success', "Tasas de IVA para {$countryCode} actualizadas correctamente.");
    }
    
    /**
     * Set default country for tax calculations.
     */
    public function setDefaultCountry(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|size:2',
        ]);
        
        $setting = Setting::where('key', 'default_country_tax')->first();
        if (!$setting) {
            Setting::create([
                'key' => 'default_country_tax',
                'value' => $request->country_code,
                'type' => 'string',
            ]);
        } else {
            $setting->update(['value' => $request->country_code]);
        }
        
        return redirect()->route('tax-settings.index')
            ->with('success', 'País por defecto actualizado correctamente.');
    }
    
    /**
     * Update service tax settings.
     */
    public function updateServiceSettings(Request $request)
    {
        $request->validate([
            'tax_includes_services' => 'required|boolean',
            'tax_includes_transport' => 'required|boolean',
        ]);
        
        foreach ($request->only(['tax_includes_services', 'tax_includes_transport']) as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if (!$setting) {
                Setting::create([
                    'key' => $key,
                    'value' => $value ? 'true' : 'false',
                    'type' => 'boolean',
                ]);
            } else {
                $setting->update(['value' => $value ? 'true' : 'false']);
            }
        }
        
        return redirect()->route('tax-settings.index')
            ->with('success', 'Configuraciones de servicios actualizadas correctamente.');
    }
    
    /**
     * Get tax rates for a specific country (API endpoint).
     */
    public function getCountryRates(string $countryCode)
    {
        $setting = Setting::where('key', "tax_rates_{$countryCode}")->first();
        $rates = $setting ? json_decode($setting->value, true) : [];
        
        return response()->json([
            'country_code' => $countryCode,
            'rates' => $rates,
        ]);
    }
    
    /**
     * Restaurar tasas por defecto para un país.
     */
    public function restoreDefaultRates(Request $request, string $countryCode)
    {
        // Obtener tasas por defecto del helper
        $defaultRates = TaxRateDefaults::getTaxRatesForCountry($countryCode);
        if (!$defaultRates) {
            return response()->json(['success' => false, 'message' => 'No hay tasas por defecto para este país.'], 404);
        }
        // Sobrescribir en settings
        $setting = Setting::where('key', "tax_rates_{$countryCode}")->first();
        if (!$setting) {
            Setting::create([
                'key' => "tax_rates_{$countryCode}",
                'value' => json_encode($defaultRates),
                'type' => 'json',
            ]);
        } else {
            $setting->update(['value' => json_encode($defaultRates)]);
        }
        return response()->json(['success' => true, 'message' => 'Tasas restauradas correctamente.', 'rates' => $defaultRates]);
    }
}
