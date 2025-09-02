<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCalendarSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $calendarSetting = $this->route('calendar_setting');
        
        // Check if the user is authorized to update this calendar setting
        // Only allow if the calendar setting belongs to the user's owner company
        return Auth::check() && $calendarSetting && 
               $calendarSetting->owner_company_id == session('owner_company_id');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:crm_users,user_id',
            'google_calendar_id' => 'required|string|max:255',
            'is_primary' => 'boolean',
            'auto_sync' => 'boolean',
            'sync_frequency_minutes' => 'integer|min:5|max:1440', // Between 5 minutes and 24 hours
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'user',
            'google_calendar_id' => 'Google Calendar ID',
            'is_primary' => 'primary calendar flag',
            'auto_sync' => 'auto sync flag',
            'sync_frequency_minutes' => 'sync frequency in minutes',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'google_calendar_id.required' => 'The Google Calendar ID is required.',
            'sync_frequency_minutes.min' => 'The sync frequency must be at least 5 minutes.',
            'sync_frequency_minutes.max' => 'The sync frequency cannot exceed 24 hours (1440 minutes).',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // If updating to a company-wide setting, ensure user_id is null
        if ($this->has('is_company_wide') && $this->input('is_company_wide')) {
            $this->merge([
                'user_id' => null,
            ]);
        }
        
        // If this is being set as primary, we need to handle that in the controller
        // by unsetting any other primary calendars for the same user or company
    }
}