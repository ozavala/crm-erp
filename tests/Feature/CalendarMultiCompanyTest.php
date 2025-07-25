<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\CalendarEvent;
use App\Models\CalendarSetting;
use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\OwnerCompany;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CalendarMultiCompanyTest extends TestCase
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
            'delete-appointments',
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'delete-tasks'
        ]);

        $this->givePermission($this->user2, [
            'view-calendar',
            'manage-calendar',
            'view-appointments',
            'create-appointments',
            'edit-appointments',
            'delete-appointments',
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'delete-tasks'
        ]);

        $this->givePermission($this->superAdmin, [
            'view-calendar',
            'manage-calendar',
            'view-appointments',
            'create-appointments',
            'edit-appointments',
            'delete-appointments',
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'delete-tasks',
            'manage-companies'
        ]);

        // Create calendar settings for each company
        $this->calendarSetting1 = CalendarSetting::create([
            'owner_company_id' => $this->company1->owner_company_id,
            'user_id' => $this->user1->user_id,
            'google_calendar_id' => 'company1@gmail.com',
            'google_calendar_api_key' => 'api_key_1',
            'google_calendar_client_id' => 'client_id_1',
            'google_calendar_client_secret' => 'client_secret_1',
            'google_calendar_refresh_token' => 'refresh_token_1',
            'google_calendar_access_token' => 'access_token_1',
            'google_calendar_token_expires' => now()->addDay(),
            'sync_enabled' => true,
            'default_reminder_time' => 30, // minutes
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '17:00:00',
            'working_days' => json_encode([1, 2, 3, 4, 5]), // Monday to Friday
        ]);

        $this->calendarSetting2 = CalendarSetting::create([
            'owner_company_id' => $this->company2->owner_company_id,
            'user_id' => $this->user2->user_id,
            'google_calendar_id' => 'company2@gmail.com',
            'google_calendar_api_key' => 'api_key_2',
            'google_calendar_client_id' => 'client_id_2',
            'google_calendar_client_secret' => 'client_secret_2',
            'google_calendar_refresh_token' => 'refresh_token_2',
            'google_calendar_access_token' => 'access_token_2',
            'google_calendar_token_expires' => now()->addDay(),
            'sync_enabled' => true,
            'default_reminder_time' => 15, // minutes
            'working_hours_start' => '08:00:00',
            'working_hours_end' => '16:00:00',
            'working_days' => json_encode([1, 2, 3, 4, 5]), // Monday to Friday
        ]);
    }

    #[Test]
    public function calendar_settings_are_isolated_between_companies()
    {
        // Verify that company 1 user can only see company 1 calendar settings
        $this->actingAs($this->user1);
        $response = $this->get(route('calendar-settings.index'));
        $response->assertOk();
        $response->assertSee('company1@gmail.com');
        $response->assertDontSee('company2@gmail.com');

        // Verify that company 2 user can only see company 2 calendar settings
        $this->actingAs($this->user2);
        $response = $this->get(route('calendar-settings.index'));
        $response->assertOk();
        $response->assertSee('company2@gmail.com');
        $response->assertDontSee('company1@gmail.com');

        // Verify that super admin can see both companies' calendar settings
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('calendar-settings.index'));
        $response->assertOk();
        $response->assertSee('company1@gmail.com');
        $response->assertSee('company2@gmail.com');
    }

    #[Test]
    public function appointments_are_isolated_between_companies()
    {
        // Create appointments for company 1
        $this->actingAs($this->user1);
        $appointment1 = Appointment::create([
            'title' => 'Company 1 Appointment',
            'description' => 'Description for company 1 appointment',
            'start_time' => now()->addDay()->setHour(10),
            'end_time' => now()->addDay()->setHour(11),
            'location' => 'Office',
            'status' => 'Scheduled',
            'created_by_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create appointments for company 2
        $this->actingAs($this->user2);
        $appointment2 = Appointment::create([
            'title' => 'Company 2 Appointment',
            'description' => 'Description for company 2 appointment',
            'start_time' => now()->addDay()->setHour(14),
            'end_time' => now()->addDay()->setHour(15),
            'location' => 'Office',
            'status' => 'Scheduled',
            'created_by_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Verify that company 1 user can only see company 1 appointments
        $this->actingAs($this->user1);
        $response = $this->get(route('appointments.index'));
        $response->assertOk();
        $response->assertSee('Company 1 Appointment');
        $response->assertDontSee('Company 2 Appointment');

        // Verify that company 2 user can only see company 2 appointments
        $this->actingAs($this->user2);
        $response = $this->get(route('appointments.index'));
        $response->assertOk();
        $response->assertSee('Company 2 Appointment');
        $response->assertDontSee('Company 1 Appointment');

        // Verify that super admin can see both companies' appointments
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('appointments.index'));
        $response->assertOk();
        $response->assertSee('Company 1 Appointment');
        $response->assertSee('Company 2 Appointment');
    }

    #[Test]
    public function tasks_are_isolated_between_companies()
    {
        // Create tasks for company 1
        $this->actingAs($this->user1);
        $task1 = Task::create([
            'title' => 'Company 1 Task',
            'description' => 'Description for company 1 task',
            'due_date' => now()->addDays(3),
            'status' => 'Not Started',
            'priority' => 'Medium',
            'assigned_to_user_id' => $this->user1->user_id,
            'created_by_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create tasks for company 2
        $this->actingAs($this->user2);
        $task2 = Task::create([
            'title' => 'Company 2 Task',
            'description' => 'Description for company 2 task',
            'due_date' => now()->addDays(5),
            'status' => 'Not Started',
            'priority' => 'High',
            'assigned_to_user_id' => $this->user2->user_id,
            'created_by_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Verify that company 1 user can only see company 1 tasks
        $this->actingAs($this->user1);
        $response = $this->get(route('tasks.index'));
        $response->assertOk();
        $response->assertSee('Company 1 Task');
        $response->assertDontSee('Company 2 Task');

        // Verify that company 2 user can only see company 2 tasks
        $this->actingAs($this->user2);
        $response = $this->get(route('tasks.index'));
        $response->assertOk();
        $response->assertSee('Company 2 Task');
        $response->assertDontSee('Company 1 Task');

        // Verify that super admin can see both companies' tasks
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('tasks.index'));
        $response->assertOk();
        $response->assertSee('Company 1 Task');
        $response->assertSee('Company 2 Task');
    }

    #[Test]
    public function calendar_events_are_isolated_between_companies()
    {
        // Create calendar events for company 1
        $this->actingAs($this->user1);
        $event1 = CalendarEvent::create([
            'title' => 'Company 1 Event',
            'description' => 'Description for company 1 event',
            'start_time' => now()->addDay()->setHour(10),
            'end_time' => now()->addDay()->setHour(11),
            'all_day' => false,
            'location' => 'Office',
            'event_type' => 'Meeting',
            'google_calendar_event_id' => 'google_event_id_1',
            'created_by_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create calendar events for company 2
        $this->actingAs($this->user2);
        $event2 = CalendarEvent::create([
            'title' => 'Company 2 Event',
            'description' => 'Description for company 2 event',
            'start_time' => now()->addDay()->setHour(14),
            'end_time' => now()->addDay()->setHour(15),
            'all_day' => false,
            'location' => 'Office',
            'event_type' => 'Meeting',
            'google_calendar_event_id' => 'google_event_id_2',
            'created_by_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Verify that company 1 user can only see company 1 calendar events
        $this->actingAs($this->user1);
        $response = $this->get(route('calendar-events.index'));
        $response->assertOk();
        $response->assertSee('Company 1 Event');
        $response->assertDontSee('Company 2 Event');

        // Verify that company 2 user can only see company 2 calendar events
        $this->actingAs($this->user2);
        $response = $this->get(route('calendar-events.index'));
        $response->assertOk();
        $response->assertSee('Company 2 Event');
        $response->assertDontSee('Company 1 Event');

        // Verify that super admin can see both companies' calendar events
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('calendar-events.index'));
        $response->assertOk();
        $response->assertSee('Company 1 Event');
        $response->assertSee('Company 2 Event');
    }

    #[Test]
    public function google_calendar_integration_respects_company_isolation()
    {
        // Mock the Google Calendar service
        $this->mock('App\Services\GoogleCalendarService', function ($mock) {
            $mock->shouldReceive('syncEvents')
                ->andReturn(['success' => true, 'message' => 'Events synced successfully']);
        });

        // Sync calendar for company 1
        $this->actingAs($this->user1);
        $response = $this->post(route('calendar.sync'));
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Events synced successfully');

        // Verify that the correct calendar settings were used
        $this->assertEquals($this->calendarSetting1->google_calendar_id, session('last_synced_calendar_id'));

        // Sync calendar for company 2
        $this->actingAs($this->user2);
        $response = $this->post(route('calendar.sync'));
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Events synced successfully');

        // Verify that the correct calendar settings were used
        $this->assertEquals($this->calendarSetting2->google_calendar_id, session('last_synced_calendar_id'));
    }

    #[Test]
    public function users_cannot_create_appointments_for_other_companies()
    {
        // Create a customer for company 1
        $customer1 = Customer::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Try to create an appointment for company 2 as company 1 user
        $this->actingAs($this->user1);
        
        $appointmentData = [
            'title' => 'Test Appointment',
            'description' => 'Test Description',
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
            'location' => 'Office',
            'status' => 'Scheduled',
            'owner_company_id' => $this->company2->owner_company_id, // Trying to set company 2
            'participants' => [
                [
                    'participant_type' => 'customer',
                    'participant_id' => $customer1->customer_id,
                ]
            ]
        ];
        
        $response = $this->post(route('appointments.store'), $appointmentData);
        
        // The request should fail because the user cannot create appointments for other companies
        $response->assertSessionHasErrors(['owner_company_id']);
        
        // Verify that no appointment was created
        $this->assertDatabaseMissing('appointments', [
            'title' => 'Test Appointment',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }

    #[Test]
    public function super_admin_can_create_appointments_for_any_company()
    {
        // Create customers for both companies
        $customer1 = Customer::factory()->create([
            'owner_company_id' => $this->company1->owner_company_id,
        ]);
        
        $customer2 = Customer::factory()->create([
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Super admin should be able to create appointments for any company
        $this->actingAs($this->superAdmin);
        
        // Create appointment for company 1
        $appointmentData1 = [
            'title' => 'Company 1 Appointment',
            'description' => 'Test Description',
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
            'location' => 'Office',
            'status' => 'Scheduled',
            'owner_company_id' => $this->company1->owner_company_id,
            'participants' => [
                [
                    'participant_type' => 'customer',
                    'participant_id' => $customer1->customer_id,
                ]
            ]
        ];
        
        $response = $this->post(route('appointments.store'), $appointmentData1);
        $response->assertRedirect();
        
        // Verify that the appointment was created
        $this->assertDatabaseHas('appointments', [
            'title' => 'Company 1 Appointment',
            'owner_company_id' => $this->company1->owner_company_id,
        ]);
        
        // Create appointment for company 2
        $appointmentData2 = [
            'title' => 'Company 2 Appointment',
            'description' => 'Test Description',
            'start_time' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->addHours(3)->format('Y-m-d H:i:s'),
            'location' => 'Office',
            'status' => 'Scheduled',
            'owner_company_id' => $this->company2->owner_company_id,
            'participants' => [
                [
                    'participant_type' => 'customer',
                    'participant_id' => $customer2->customer_id,
                ]
            ]
        ];
        
        $response = $this->post(route('appointments.store'), $appointmentData2);
        $response->assertRedirect();
        
        // Verify that the appointment was created
        $this->assertDatabaseHas('appointments', [
            'title' => 'Company 2 Appointment',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }
}