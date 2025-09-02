<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\CalendarEvent;
use App\Models\CalendarSetting;
use App\Models\CrmUser;
use App\Models\OwnerCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Mockery;

class GoogleCalendarMultiCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected OwnerCompany $company1;
    protected OwnerCompany $company2;
    protected CrmUser $user1;
    protected CrmUser $user2;
    protected CrmUser $superAdmin;
    protected CalendarSetting $calendarSetting1;
    protected CalendarSetting $calendarSetting2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SettingsTableSeeder::class);

        // Create two companies
        $this->company1 = OwnerCompany::create([
            'name' => 'Company One',
            'legal_name' => 'Company One LLC',
            'tax_id' => 'TAX-001',
            'email' => 'info@company1.com',
            'phone' => '123-456-7890',
            'address' => '123 Main St, Anytown, USA',
            'is_active' => true,
        ]);

        $this->company2 = OwnerCompany::create([
            'name' => 'Company Two',
            'legal_name' => 'Company Two Inc',
            'tax_id' => 'TAX-002',
            'email' => 'info@company2.com',
            'phone' => '987-654-3210',
            'address' => '456 Oak Ave, Somewhere, USA',
            'is_active' => true,
        ]);

        // Create users for each company
        $this->user1 = CrmUser::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->user2 = CrmUser::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create a super admin user
        $this->superAdmin = CrmUser::factory()->create([
            'is_super_admin' => true,
            'owner_company_id' => $this->company1->owner_company_id, // Primary company
        ]);

        // Give necessary permissions to users
        $this->givePermission($this->user1, [
            'view-calendar',
            'manage-calendar',
            'view-appointments',
            'create-appointments',
            'edit-appointments',
            'delete-appointments'
        ]);

        $this->givePermission($this->user2, [
            'view-calendar',
            'manage-calendar',
            'view-appointments',
            'create-appointments',
            'edit-appointments',
            'delete-appointments'
        ]);

        $this->givePermission($this->superAdmin, [
            'view-calendar',
            'manage-calendar',
            'view-appointments',
            'create-appointments',
            'edit-appointments',
            'delete-appointments',
            'manage-companies'
        ]);

        // Create calendar settings for each company
        $this->calendarSetting1 = CalendarSetting::create([
            'user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
            'google_calendar_id' => 'company1@group.calendar.google.com',
            'is_primary' => true,
            'sync_enabled' => true,
            'display_name' => 'Company One Calendar',
            'color' => '#FF0000',
        ]);

        $this->calendarSetting2 = CalendarSetting::create([
            'user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
            'google_calendar_id' => 'company2@group.calendar.google.com',
            'is_primary' => true,
            'sync_enabled' => true,
            'display_name' => 'Company Two Calendar',
            'color' => '#0000FF',
        ]);

        // Mock the Google Calendar service
        $this->mockGoogleCalendarService();
    }

    protected function mockGoogleCalendarService()
    {
        // Create a mock for the Google Calendar service
        $googleCalendarMock = Mockery::mock('Spatie\GoogleCalendar\GoogleCalendar');
        $googleCalendarMock->shouldReceive('getEvent')->andReturn(null);
        $googleCalendarMock->shouldReceive('insertEvent')->andReturn(null);
        $googleCalendarMock->shouldReceive('updateEvent')->andReturn(null);
        $googleCalendarMock->shouldReceive('deleteEvent')->andReturn(null);
        
        // Bind the mock to the container
        $this->app->instance('Spatie\GoogleCalendar\GoogleCalendar', $googleCalendarMock);
        
        // Mock the config to use a test calendar ID
        Config::set('google-calendar.calendar_id', 'test_calendar_id');
    }

    #[Test]
    public function users_can_only_see_calendar_events_from_their_company()
    {
        // Create appointments for company 1
        $company1Appointments = [];
        for ($i = 0; $i < 3; $i++) {
            $appointment = Appointment::create([
                'title' => "Company 1 Appointment $i",
                'description' => "Description for appointment $i",
                'start_time' => now()->addDays($i)->setHour(10),
                'end_time' => now()->addDays($i)->setHour(11),
                'location' => 'Office',
                'status' => 'Scheduled',
                'created_by_user_id' => $this->user1->user_id,
                'owner_company_id' => $this->company1->owner_company_id,
            ]);
            
            // Create calendar event for the appointment
            CalendarEvent::create([
                'title' => $appointment->title,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'all_day' => false,
                'location' => $appointment->location,
                'description' => $appointment->description,
                'google_event_id' => "google_event_company1_$i",
                'calendar_id' => $this->calendarSetting1->google_calendar_id,
                'eventable_id' => $appointment->appointment_id,
                'eventable_type' => Appointment::class,
                'owner_company_id' => $this->company1->owner_company_id,
                'created_by_user_id' => $this->user1->user_id,
            ]);
            
            $company1Appointments[] = $appointment;
        }

        // Create appointments for company 2
        $company2Appointments = [];
        for ($i = 0; $i < 2; $i++) {
            $appointment = Appointment::create([
                'title' => "Company 2 Appointment $i",
                'description' => "Description for appointment $i",
                'start_time' => now()->addDays($i)->setHour(14),
                'end_time' => now()->addDays($i)->setHour(15),
                'location' => 'Office',
                'status' => 'Scheduled',
                'created_by_user_id' => $this->user2->user_id,
                'owner_company_id' => $this->company2->owner_company_id,
            ]);
            
            // Create calendar event for the appointment
            CalendarEvent::create([
                'title' => $appointment->title,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'all_day' => false,
                'location' => $appointment->location,
                'description' => $appointment->description,
                'google_event_id' => "google_event_company2_$i",
                'calendar_id' => $this->calendarSetting2->google_calendar_id,
                'eventable_id' => $appointment->appointment_id,
                'eventable_type' => Appointment::class,
                'owner_company_id' => $this->company2->owner_company_id,
                'created_by_user_id' => $this->user2->user_id,
            ]);
            
            $company2Appointments[] = $appointment;
        }

        // User 1 should only see company 1 calendar events
        $this->actingAs($this->user1);
        $response = $this->get(route('calendar.index'));
        $response->assertOk();
        
        foreach ($company1Appointments as $appointment) {
            $response->assertSee($appointment->title);
        }
        
        foreach ($company2Appointments as $appointment) {
            $response->assertDontSee($appointment->title);
        }

        // User 2 should only see company 2 calendar events
        $this->actingAs($this->user2);
        $response = $this->get(route('calendar.index'));
        $response->assertOk();
        
        foreach ($company1Appointments as $appointment) {
            $response->assertDontSee($appointment->title);
        }
        
        foreach ($company2Appointments as $appointment) {
            $response->assertSee($appointment->title);
        }

        // Super admin should see all calendar events
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('calendar.index'));
        $response->assertOk();
        
        foreach ($company1Appointments as $appointment) {
            $response->assertSee($appointment->title);
        }
        
        foreach ($company2Appointments as $appointment) {
            $response->assertSee($appointment->title);
        }
    }

    #[Test]
    public function users_cannot_access_appointments_from_other_companies()
    {
        // Create an appointment for company 1
        $company1Appointment = Appointment::create([
            'title' => "Company 1 Appointment",
            'description' => "Description for company 1 appointment",
            'start_time' => now()->addDay()->setHour(10),
            'end_time' => now()->addDay()->setHour(11),
            'location' => 'Office',
            'status' => 'Scheduled',
            'created_by_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create an appointment for company 2
        $company2Appointment = Appointment::create([
            'title' => "Company 2 Appointment",
            'description' => "Description for company 2 appointment",
            'start_time' => now()->addDay()->setHour(14),
            'end_time' => now()->addDay()->setHour(15),
            'location' => 'Office',
            'status' => 'Scheduled',
            'created_by_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // User 1 should be able to access company 1 appointment
        $this->actingAs($this->user1);
        $response = $this->get(route('appointments.show', $company1Appointment));
        $response->assertOk();

        // User 1 should not be able to access company 2 appointment
        $response = $this->get(route('appointments.show', $company2Appointment));
        $response->assertForbidden();

        // User 2 should be able to access company 2 appointment
        $this->actingAs($this->user2);
        $response = $this->get(route('appointments.show', $company2Appointment));
        $response->assertOk();

        // User 2 should not be able to access company 1 appointment
        $response = $this->get(route('appointments.show', $company1Appointment));
        $response->assertForbidden();

        // Super admin should be able to access both appointments
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('appointments.show', $company1Appointment));
        $response->assertOk();
        $response = $this->get(route('appointments.show', $company2Appointment));
        $response->assertOk();
    }

    #[Test]
    public function calendar_events_are_associated_with_correct_company()
    {
        $this->actingAs($this->user1);

        // Create an appointment for company 1
        $appointmentData = [
            'title' => 'Test Appointment',
            'description' => 'Test Description',
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
            'location' => 'Test Location',
            'status' => 'Scheduled',
            'participants' => [],
            'sync_with_google' => true,
        ];

        $response = $this->post(route('appointments.store'), $appointmentData);
        $response->assertRedirect();

        // Get the created appointment
        $appointment = Appointment::where('title', 'Test Appointment')->first();
        $this->assertNotNull($appointment);
        
        // Check that the appointment is associated with the correct company
        $this->assertEquals($this->company1->owner_company_id, $appointment->owner_company_id);
        
        // Check that a calendar event was created for the appointment
        $calendarEvent = CalendarEvent::where('eventable_id', $appointment->appointment_id)
            ->where('eventable_type', Appointment::class)
            ->first();
            
        $this->assertNotNull($calendarEvent);
        
        // Check that the calendar event is associated with the correct company
        $this->assertEquals($this->company1->owner_company_id, $calendarEvent->owner_company_id);
        
        // Check that the calendar event is associated with the correct calendar
        $this->assertEquals($this->calendarSetting1->google_calendar_id, $calendarEvent->calendar_id);
    }

    #[Test]
    public function users_can_only_see_their_company_calendar_settings()
    {
        // User 1 should only see company 1 calendar settings
        $this->actingAs($this->user1);
        $response = $this->get(route('calendar-settings.index'));
        $response->assertOk();
        $response->assertSee($this->calendarSetting1->display_name);
        $response->assertDontSee($this->calendarSetting2->display_name);

        // User 2 should only see company 2 calendar settings
        $this->actingAs($this->user2);
        $response = $this->get(route('calendar-settings.index'));
        $response->assertOk();
        $response->assertSee($this->calendarSetting2->display_name);
        $response->assertDontSee($this->calendarSetting1->display_name);

        // Super admin should see all calendar settings
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('calendar-settings.index'));
        $response->assertOk();
        $response->assertSee($this->calendarSetting1->display_name);
        $response->assertSee($this->calendarSetting2->display_name);
    }

    #[Test]
    public function users_cannot_create_calendar_settings_for_other_companies()
    {
        $this->actingAs($this->user1);

        $calendarSettingData = [
            'user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company2->owner_company_id, // Trying to create for company 2
            'google_calendar_id' => 'user1@group.calendar.google.com',
            'is_primary' => false,
            'sync_enabled' => true,
            'display_name' => 'User 1 Calendar',
            'color' => '#00FF00',
        ];

        $response = $this->post(route('calendar-settings.store'), $calendarSettingData);
        
        // The request should succeed because the controller should override the owner_company_id
        $response->assertRedirect();
        
        // But the calendar setting should be created for company 1, not company 2
        $this->assertDatabaseHas('calendar_settings', [
            'display_name' => 'User 1 Calendar',
            'owner_company_id' => $this->company1->owner_company_id,
        ]);
        
        $this->assertDatabaseMissing('calendar_settings', [
            'display_name' => 'User 1 Calendar',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}