<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseCompanyController;
use App\Models\CalendarSetting;
use App\Models\CrmUser;
use App\Http\Requests\StoreCalendarSettingRequest;
use App\Http\Requests\UpdateCalendarSettingRequest;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarSettingController extends BaseCompanyController
{
    /**
     * Display a listing of the calendar settings.
     */
    public function index()
    {
        // Get company-wide settings
        $companySettings = CalendarSetting::with('ownerCompany')->whereNull('user_id')->get();
            
        // Get user-specific settings
        // The CompanyScope is automatically applied here.
        $userSettings = CalendarSetting::with(['ownerCompany', 'user'])->whereNotNull('user_id')->get();
            
        return view('calendar_settings.index', compact('companySettings', 'userSettings'));
    }

    /**
     * Show the form for creating a new calendar setting.
     */
    public function create()
    {
        // Get users for dropdown
        // Note: Applying the CompanyScope to the CrmUser model would simplify this as well.
        $users = CrmUser::where('owner_company_id', $this->currentCompanyId)->get();
        
        return view('calendar_settings.create', compact('users'));
    }

    /**
     * Store a newly created calendar setting in storage.
     */
    public function store(StoreCalendarSettingRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['owner_company_id'] = $this->currentCompanyId;
        
        DB::transaction(function () use ($validatedData) {
            // If this is a primary calendar, unset any other primary calendars
            if (isset($validatedData['is_primary']) && $validatedData['is_primary']) {
                $this->unsetOtherPrimaryCalendars($validatedData['user_id']);
            }
            
            // Create the calendar setting
            CalendarSetting::create($validatedData);
        });
        
        return redirect()->route('calendar-settings.index')
            ->with('success', 'Calendar setting created successfully.');
    }

    /**
     * Display the specified calendar setting.
     */
    public function show(CalendarSetting $calendarSetting)
    {
        // Authorization is now handled automatically by Route Model Binding + CompanyScope.
        
        // Load relationships
        $calendarSetting->load(['ownerCompany', 'user']);
        
        return view('calendar_settings.show', compact('calendarSetting'));
    }

    /**
     * Show the form for editing the specified calendar setting.
     */
    public function edit(CalendarSetting $calendarSetting)
    {
        // Authorization is now handled automatically by Route Model Binding + CompanyScope.
        
        // Get users for dropdown
        // Note: Applying the CompanyScope to the CrmUser model would simplify this as well.
        $users = CrmUser::where('owner_company_id', $this->currentCompanyId)->get();
        
        return view('calendar_settings.edit', compact('calendarSetting', 'users'));
    }

    /**
     * Update the specified calendar setting in storage.
     */
    public function update(UpdateCalendarSettingRequest $request, CalendarSetting $calendarSetting)
    {
        // Authorization is now handled automatically by Route Model Binding + CompanyScope.
        $validatedData = $request->validated();
        
        DB::transaction(function () use ($calendarSetting, $validatedData) {
            // If this is being set as primary, unset any other primary calendars
            if (isset($validatedData['is_primary']) && $validatedData['is_primary'] && !$calendarSetting->is_primary) {
                $this->unsetOtherPrimaryCalendars($validatedData['user_id']);
            }
            
            // Update the calendar setting
            $calendarSetting->update($validatedData);
        });
        
        return redirect()->route('calendar-settings.show', $calendarSetting)
            ->with('success', 'Calendar setting updated successfully.');
    }

    /**
     * Remove the specified calendar setting from storage.
     */
    public function destroy(CalendarSetting $calendarSetting)
    {
        // Authorization is now handled automatically by Route Model Binding + CompanyScope.
        
        // Delete the calendar setting
        $calendarSetting->delete();
        
        return redirect()->route('calendar-settings.index')
            ->with('success', 'Calendar setting deleted successfully.');
    }

    /**
     * Unset other primary calendars for the same user or company.
     */
    private function unsetOtherPrimaryCalendars($userId)
    {
        $query = CalendarSetting::where('is_primary', true);
            
        if ($userId) {
            // If this is a user-specific calendar, only unset other primary calendars for this user
            $query->where('user_id', $userId);
        } else {
            // If this is a company-wide calendar, only unset other primary company-wide calendars
            $query->whereNull('user_id');
        }
        
        $query->update(['is_primary' => false]);
    }

    /**
     * Test the connection to Google Calendar.
     */
    public function testConnection(CalendarSetting $calendarSetting, GoogleCalendarService $googleCalendarService)
    {
        // Authorization is now handled automatically by Route Model Binding + CompanyScope.
        
        $success = $googleCalendarService->testConnection($calendarSetting->google_calendar_id);
        
        if ($success) {
            return redirect()->route('calendar-settings.show', $calendarSetting)
                ->with('success', 'Connection to Google Calendar successful.');
        } else {
            return redirect()->route('calendar-settings.show', $calendarSetting)
                ->with('error', 'Failed to connect to Google Calendar. Please check your calendar ID and credentials.');
        }
    }

    /**
     * Sync events with Google Calendar.
     */
    public function syncEvents(CalendarSetting $calendarSetting, GoogleCalendarService $googleCalendarService)
    {
        // Authorization is now handled automatically by Route Model Binding + CompanyScope.
        
        // Get all pending calendar events for this calendar
        $pendingEvents = \App\Models\CalendarEvent::where('google_calendar_id', $calendarSetting->google_calendar_id)
            ->where('sync_status', 'pending')
            ->get();
            
        $succeeded = 0;
        $failed = 0;
        
        foreach ($pendingEvents as $event) {
            $success = false;
            
            if ($event->related_type === 'appointment') {
                $success = $googleCalendarService->syncAppointmentToGoogle($event);
            } elseif ($event->related_type === 'task') {
                $success = $googleCalendarService->syncTaskToGoogle($event);
            }
            
            if ($success) {
                $succeeded++;
            } else {
                $failed++;
            }
        }
        
        if ($pendingEvents->count() > 0) {
            return redirect()->route('calendar-settings.show', $calendarSetting)
                ->with('success', "Synced {$succeeded} of {$pendingEvents->count()} calendar events successfully. {$failed} failed.");
        } else {
            return redirect()->route('calendar-settings.show', $calendarSetting)
                ->with('info', 'No pending calendar events to sync.');
        }
    }
}