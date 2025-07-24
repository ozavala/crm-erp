<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $appointment = $this->route('appointment');
        
        // Check if the user is authorized to update this appointment
        // Only allow if the appointment belongs to the user's owner company
        return Auth::check() && $appointment && 
               $appointment->owner_company_id == session('owner_company_id');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'all_day' => 'boolean',
            'status' => ['required', Rule::in(Appointment::$statuses)],
            'google_calendar_event_id' => 'nullable|string|max:255',
            
            // Participant arrays
            'user_participants' => 'nullable|array',
            'user_participants.*' => 'exists:crm_users,user_id',
            'customer_participants' => 'nullable|array',
            'customer_participants.*' => 'exists:customers,customer_id',
            'contact_participants' => 'nullable|array',
            'contact_participants.*' => 'exists:contacts,contact_id',
            
            // Google Calendar integration
            'sync_to_google_calendar' => 'nullable|boolean',
            'google_calendar_id' => 'required_if:sync_to_google_calendar,1|string',
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
            'title' => 'appointment title',
            'description' => 'appointment description',
            'location' => 'appointment location',
            'start_datetime' => 'start date and time',
            'end_datetime' => 'end date and time',
            'all_day' => 'all day flag',
            'status' => 'appointment status',
            'google_calendar_event_id' => 'Google Calendar event ID',
            'user_participants' => 'user participants',
            'user_participants.*' => 'user participant',
            'customer_participants' => 'customer participants',
            'customer_participants.*' => 'customer participant',
            'contact_participants' => 'contact participants',
            'contact_participants.*' => 'contact participant',
            'sync_to_google_calendar' => 'sync to Google Calendar',
            'google_calendar_id' => 'Google Calendar ID',
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
            'title.required' => 'The appointment title is required.',
            'start_datetime.required' => 'The start date and time is required.',
            'end_datetime.required' => 'The end date and time is required.',
            'end_datetime.after_or_equal' => 'The end date and time must be after or equal to the start date and time.',
            'status.required' => 'The appointment status is required.',
            'status.in' => 'The selected appointment status is invalid.',
            'google_calendar_id.required_if' => 'The Google Calendar ID is required when syncing to Google Calendar.',
        ];
    }
}