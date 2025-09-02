<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\OwnerCompany;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationMultiCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected OwnerCompany $company1;
    protected OwnerCompany $company2;
    protected CrmUser $user1;
    protected CrmUser $user2;
    protected CrmUser $superAdmin;
    protected Customer $customer1;
    protected Customer $customer2;
    protected Appointment $appointment1;
    protected Appointment $appointment2;
    protected Task $task1;
    protected Task $task2;
    protected Invoice $invoice1;
    protected Invoice $invoice2;

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
            'name' => 'User One',
            'email' => 'user1@company1.com',
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->user2 = CrmUser::factory()->create([
            'name' => 'User Two',
            'email' => 'user2@company2.com',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create a super admin user
        $this->superAdmin = CrmUser::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@system.com',
            'is_super_admin' => true,
            'owner_company_id' => $this->company1->owner_company_id, // Primary company
        ]);

        // Create customers for each company
        $this->customer1 = Customer::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->customer2 = Customer::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create appointments for each company
        $this->appointment1 = Appointment::create([
            'title' => 'Appointment for Company 1',
            'description' => 'Description for appointment 1',
            'start_time' => now()->addDay()->setHour(10),
            'end_time' => now()->addDay()->setHour(11),
            'location' => 'Office',
            'status' => 'Scheduled',
            'created_by_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->appointment2 = Appointment::create([
            'title' => 'Appointment for Company 2',
            'description' => 'Description for appointment 2',
            'start_time' => now()->addDay()->setHour(14),
            'end_time' => now()->addDay()->setHour(15),
            'location' => 'Office',
            'status' => 'Scheduled',
            'created_by_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create tasks for each company
        $this->task1 = Task::create([
            'title' => 'Task for Company 1',
            'description' => 'Description for task 1',
            'due_date' => now()->addDays(3),
            'status' => 'Not Started',
            'priority' => 'Medium',
            'assigned_to_user_id' => $this->user1->user_id,
            'created_by_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->task2 = Task::create([
            'title' => 'Task for Company 2',
            'description' => 'Description for task 2',
            'due_date' => now()->addDays(5),
            'status' => 'Not Started',
            'priority' => 'High',
            'assigned_to_user_id' => $this->user2->user_id,
            'created_by_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create invoices for each company
        $this->invoice1 = Invoice::create([
            'customer_id' => $this->customer1->customer_id,
            'invoice_number' => 'INV-001-C1',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'Pending',
            'notes' => 'Invoice for Company 1',
            'created_by_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->invoice2 = Invoice::create([
            'customer_id' => $this->customer2->customer_id,
            'invoice_number' => 'INV-001-C2',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'Pending',
            'notes' => 'Invoice for Company 2',
            'created_by_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create notifications for each company
        $this->createNotifications();
    }

    protected function createNotifications()
    {
        // Create appointment reminder notifications for company 1
        Notification::create([
            'user_id' => $this->user1->user_id,
            'type' => 'appointment_reminder',
            'notifiable_type' => 'App\\Models\\Appointment',
            'notifiable_id' => $this->appointment1->appointment_id,
            'data' => json_encode([
                'title' => 'Appointment Reminder',
                'message' => 'You have an appointment tomorrow: ' . $this->appointment1->title,
                'appointment_id' => $this->appointment1->appointment_id,
            ]),
            'read_at' => null,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create appointment reminder notifications for company 2
        Notification::create([
            'user_id' => $this->user2->user_id,
            'type' => 'appointment_reminder',
            'notifiable_type' => 'App\\Models\\Appointment',
            'notifiable_id' => $this->appointment2->appointment_id,
            'data' => json_encode([
                'title' => 'Appointment Reminder',
                'message' => 'You have an appointment tomorrow: ' . $this->appointment2->title,
                'appointment_id' => $this->appointment2->appointment_id,
            ]),
            'read_at' => null,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create task due notifications for company 1
        Notification::create([
            'user_id' => $this->user1->user_id,
            'type' => 'task_due',
            'notifiable_type' => 'App\\Models\\Task',
            'notifiable_id' => $this->task1->task_id,
            'data' => json_encode([
                'title' => 'Task Due Soon',
                'message' => 'Task due in 3 days: ' . $this->task1->title,
                'task_id' => $this->task1->task_id,
            ]),
            'read_at' => null,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create task due notifications for company 2
        Notification::create([
            'user_id' => $this->user2->user_id,
            'type' => 'task_due',
            'notifiable_type' => 'App\\Models\\Task',
            'notifiable_id' => $this->task2->task_id,
            'data' => json_encode([
                'title' => 'Task Due Soon',
                'message' => 'Task due in 5 days: ' . $this->task2->title,
                'task_id' => $this->task2->task_id,
            ]),
            'read_at' => null,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create invoice notifications for company 1
        Notification::create([
            'user_id' => $this->user1->user_id,
            'type' => 'invoice_created',
            'notifiable_type' => 'App\\Models\\Invoice',
            'notifiable_id' => $this->invoice1->invoice_id,
            'data' => json_encode([
                'title' => 'New Invoice Created',
                'message' => 'Invoice ' . $this->invoice1->invoice_number . ' has been created',
                'invoice_id' => $this->invoice1->invoice_id,
            ]),
            'read_at' => null,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create invoice notifications for company 2
        Notification::create([
            'user_id' => $this->user2->user_id,
            'type' => 'invoice_created',
            'notifiable_type' => 'App\\Models\\Invoice',
            'notifiable_id' => $this->invoice2->invoice_id,
            'data' => json_encode([
                'title' => 'New Invoice Created',
                'message' => 'Invoice ' . $this->invoice2->invoice_number . ' has been created',
                'invoice_id' => $this->invoice2->invoice_id,
            ]),
            'read_at' => null,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }

    #[Test]
    public function notifications_are_isolated_between_companies()
    {
        // Verify that company 1 user can only see company 1 notifications
        $this->actingAs($this->user1);
        $response = $this->get(route('notifications.index'));
        $response->assertOk();
        
        // Check for company 1 notification content
        $response->assertSee('Appointment for Company 1');
        $response->assertSee('Task for Company 1');
        $response->assertSee('INV-001-C1');
        
        // Check that company 2 notification content is not visible
        $response->assertDontSee('Appointment for Company 2');
        $response->assertDontSee('Task for Company 2');
        $response->assertDontSee('INV-001-C2');

        // Verify that company 2 user can only see company 2 notifications
        $this->actingAs($this->user2);
        $response = $this->get(route('notifications.index'));
        $response->assertOk();
        
        // Check for company 2 notification content
        $response->assertSee('Appointment for Company 2');
        $response->assertSee('Task for Company 2');
        $response->assertSee('INV-001-C2');
        
        // Check that company 1 notification content is not visible
        $response->assertDontSee('Appointment for Company 1');
        $response->assertDontSee('Task for Company 1');
        $response->assertDontSee('INV-001-C1');
    }

    #[Test]
    public function users_cannot_access_notifications_from_other_companies()
    {
        // Get company 1 notification
        $notification1 = Notification::where('user_id', $this->user1->user_id)->first();
        
        // Get company 2 notification
        $notification2 = Notification::where('user_id', $this->user2->user_id)->first();

        // Company 1 user tries to access company 2 notification
        $this->actingAs($this->user1);
        $response = $this->get(route('notifications.show', $notification2->id));
        $response->assertForbidden();

        // Company 2 user tries to access company 1 notification
        $this->actingAs($this->user2);
        $response = $this->get(route('notifications.show', $notification1->id));
        $response->assertForbidden();

        // Super admin can access both notifications
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('notifications.show', $notification1->id));
        $response->assertOk();
        $response = $this->get(route('notifications.show', $notification2->id));
        $response->assertOk();
    }

    #[Test]
    public function users_cannot_mark_as_read_notifications_from_other_companies()
    {
        // Get company 1 notification
        $notification1 = Notification::where('user_id', $this->user1->user_id)->first();
        
        // Get company 2 notification
        $notification2 = Notification::where('user_id', $this->user2->user_id)->first();

        // Company 1 user tries to mark company 2 notification as read
        $this->actingAs($this->user1);
        $response = $this->post(route('notifications.mark-as-read', $notification2->id));
        $response->assertForbidden();

        // Verify that the notification is still unread
        $this->assertDatabaseHas('notifications', [
            'id' => $notification2->id,
            'read_at' => null,
        ]);

        // Company 2 user tries to mark company 1 notification as read
        $this->actingAs($this->user2);
        $response = $this->post(route('notifications.mark-as-read', $notification1->id));
        $response->assertForbidden();

        // Verify that the notification is still unread
        $this->assertDatabaseHas('notifications', [
            'id' => $notification1->id,
            'read_at' => null,
        ]);

        // Super admin can mark both notifications as read
        $this->actingAs($this->superAdmin);
        $response = $this->post(route('notifications.mark-as-read', $notification1->id));
        $response->assertRedirect();
        $response = $this->post(route('notifications.mark-as-read', $notification2->id));
        $response->assertRedirect();

        // Verify that both notifications are now read
        $this->assertDatabaseMissing('notifications', [
            'id' => $notification1->id,
            'read_at' => null,
        ]);
        $this->assertDatabaseMissing('notifications', [
            'id' => $notification2->id,
            'read_at' => null,
        ]);
    }

    #[Test]
    public function notification_count_is_isolated_between_companies()
    {
        // Verify that company 1 user only sees count of company 1 notifications
        $this->actingAs($this->user1);
        $response = $this->get(route('notifications.count'));
        $response->assertOk();
        $response->assertJson(['count' => 3]); // 3 notifications for company 1

        // Verify that company 2 user only sees count of company 2 notifications
        $this->actingAs($this->user2);
        $response = $this->get(route('notifications.count'));
        $response->assertOk();
        $response->assertJson(['count' => 3]); // 3 notifications for company 2

        // Mark one notification as read for company 1
        $notification1 = Notification::where('user_id', $this->user1->user_id)->first();
        $notification1->update(['read_at' => now()]);

        // Verify that the count is updated for company 1 but not for company 2
        $this->actingAs($this->user1);
        $response = $this->get(route('notifications.count'));
        $response->assertOk();
        $response->assertJson(['count' => 2]); // 2 unread notifications for company 1

        $this->actingAs($this->user2);
        $response = $this->get(route('notifications.count'));
        $response->assertOk();
        $response->assertJson(['count' => 3]); // Still 3 notifications for company 2
    }

    #[Test]
    public function notification_creation_respects_company_isolation()
    {
        // Create a new appointment for company 1
        $newAppointment1 = Appointment::create([
            'title' => 'New Appointment for Company 1',
            'description' => 'Description for new appointment 1',
            'start_time' => now()->addDays(2)->setHour(10),
            'end_time' => now()->addDays(2)->setHour(11),
            'location' => 'Office',
            'status' => 'Scheduled',
            'created_by_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create a notification for the new appointment
        $this->actingAs($this->user1);
        $response = $this->post(route('notifications.store'), [
            'user_id' => $this->user1->user_id,
            'type' => 'appointment_reminder',
            'notifiable_type' => 'App\\Models\\Appointment',
            'notifiable_id' => $newAppointment1->appointment_id,
            'data' => json_encode([
                'title' => 'New Appointment Reminder',
                'message' => 'You have a new appointment in 2 days: ' . $newAppointment1->title,
                'appointment_id' => $newAppointment1->appointment_id,
            ]),
        ]);
        $response->assertRedirect();

        // Verify that the notification was created with the correct company ID
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user1->user_id,
            'notifiable_id' => $newAppointment1->appointment_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Try to create a notification for company 2 as company 1 user
        $response = $this->post(route('notifications.store'), [
            'user_id' => $this->user2->user_id, // Company 2 user
            'type' => 'appointment_reminder',
            'notifiable_type' => 'App\\Models\\Appointment',
            'notifiable_id' => $this->appointment2->appointment_id, // Company 2 appointment
            'data' => json_encode([
                'title' => 'Appointment Reminder',
                'message' => 'Reminder for appointment: ' . $this->appointment2->title,
                'appointment_id' => $this->appointment2->appointment_id,
            ]),
            'owner_company_id' => $this->company2->owner_company_id, // Trying to set company 2
        ]);

        // The request should fail because the user cannot create notifications for other companies
        $response->assertForbidden();

        // Verify that no notification was created for company 2
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $this->user2->user_id,
            'notifiable_id' => $this->appointment2->appointment_id,
            'owner_company_id' => $this->company2->owner_company_id,
            'data' => json_encode([
                'title' => 'Appointment Reminder',
                'message' => 'Reminder for appointment: ' . $this->appointment2->title,
                'appointment_id' => $this->appointment2->appointment_id,
            ]),
        ]);
    }

    #[Test]
    public function super_admin_can_manage_notifications_for_all_companies()
    {
        // Super admin creates a notification for company 2 user
        $this->actingAs($this->superAdmin);
        $response = $this->post(route('notifications.store'), [
            'user_id' => $this->user2->user_id, // Company 2 user
            'type' => 'system_message',
            'notifiable_type' => 'App\\Models\\System',
            'notifiable_id' => 1,
            'data' => json_encode([
                'title' => 'System Message',
                'message' => 'Important system update scheduled',
            ]),
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
        $response->assertRedirect();

        // Verify that the notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user2->user_id,
            'type' => 'system_message',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Company 2 user can see the notification
        $this->actingAs($this->user2);
        $response = $this->get(route('notifications.index'));
        $response->assertOk();
        $response->assertSee('System Message');
        $response->assertSee('Important system update scheduled');
    }
}