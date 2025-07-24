<?php

namespace App\Http\Controllers;

use App\Models\CalendarSetting;
use App\Models\CrmUser;
use App\Http\Requests\StoreCalendarSettingRequest;
use App\Http\Requests\UpdateCalendarSettingRequest;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CalendarSettingController extends Controller
{
    /**
     * Display a listing of the calendar settings.
     */
    public function index()
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        // Get company-wide settings
        $companySettings = CalendarSetting::with('ownerCompany')
            ->where('owner_company_id', $ownerCompanyId)
            ->whereNull('user_id')
            ->get();
            
        // Get user-specific settings
        $userSettings = CalendarSetting::with(['ownerCompany', 'user'])
            ->where('owner_company_id', $ownerCompanyId)
            ->whereNotNull('user_id')
            ->get();
            
        return view('calendar_settings.index', compact('companySettings', 'userSettings'));
    }

    /**
     * Show the form for creating a new calendar setting.
     */
    public function create()
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        // Get users for dropdown
        $users = CrmUser::where('owner_company_id', $ownerCompanyId)->get();
        
        return view('calendar_settings.create', compact('users'));
    }

    /**
     * Store a newly created calendar setting in storage.
     */
    public function store(StoreCalendarSettingRequest $request)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        $validatedData = $request->validated();
        $validatedData['owner_company_id'] = $ownerCompanyId;
        
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
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($calendarSetting->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load relationships
        $calendarSetting->load(['ownerCompany', 'user']);
        
        return view('calendar_settings.show', compact('calendarSetting'));
    }

    /**
     * Show the form for editing the specified calendar setting.
     */
    public function edit(CalendarSetting $calendarSetting)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($calendarSetting->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get users for dropdown
        $users = CrmUser::where('owner_company_id', $ownerCompanyId)->get();
        
        return view('calendar_settings.edit', compact('calendarSetting', 'users'));
    }

    /**
     * Update the specified calendar setting in storage.
     */
    public function update(UpdateCalendarSettingRequest $request, CalendarSetting $calendarSetting)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($calendarSetting->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
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
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($calendarSetting->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
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
        $ownerCompanyId = Session::get('owner_company_id');
        $query = CalendarSetting::where('owner_company_id', $ownerCompanyId)
            ->where('is_primary', true);
            
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
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($calendarSetting->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
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
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($calendarSetting->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get all pending calendar events for this calendar
        $pendingEvents = \App\Models\CalendarEvent::where('owner_company_id', $ownerCompanyId)
            ->where('google_calendar_id', $calendarSetting->google_calendar_id)
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