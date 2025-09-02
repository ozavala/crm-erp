<?php

namespace Tests\Feature;

use App\Models\CrmUser;
use App\Models\OwnerCompany;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserManagementMultiCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected OwnerCompany $company1;
    protected OwnerCompany $company2;
    protected CrmUser $companyAdmin1;
    protected CrmUser $companyAdmin2;
    protected CrmUser $regularUser1;
    protected CrmUser $regularUser2;
    protected CrmUser $superAdmin;
    protected Role $adminRole;
    protected Role $userRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SettingsTableSeeder::class);
        $this->seed(\Database\Seeders\PermissionSeeder::class);

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

        // Create roles
        $this->adminRole = Role::create([
            'name' => 'Company Admin',
            'description' => 'Administrator for a company',
        ]);

        $this->userRole = Role::create([
            'name' => 'Regular User',
            'description' => 'Regular user with limited permissions',
        ]);

        // Assign permissions to roles
        $adminPermissions = Permission::whereIn('name', [
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-permissions',
            'assign-permissions',
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'view-invoices',
            'create-invoices',
            'edit-invoices',
            'delete-invoices',
            'view-calendar',
            'manage-calendar',
            'view-appointments',
            'create-appointments',
            'edit-appointments',
            'delete-appointments'
        ])->get();

        $userPermissions = Permission::whereIn('name', [
            'view-customers',
            'create-customers',
            'edit-customers',
            'view-products',
            'view-invoices',
            'create-invoices',
            'view-calendar',
            'view-appointments',
            'create-appointments'
        ])->get();

        $this->adminRole->permissions()->attach($adminPermissions);
        $this->userRole->permissions()->attach($userPermissions);

        // Create users for each company
        $this->companyAdmin1 = CrmUser::factory()->create([
            'name' => 'Admin One',
            'email' => 'admin1@company1.com',
            'is_admin' => true,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->companyAdmin2 = CrmUser::factory()->create([
            'name' => 'Admin Two',
            'email' => 'admin2@company2.com',
            'is_admin' => true,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        $this->regularUser1 = CrmUser::factory()->create([
            'name' => 'User One',
            'email' => 'user1@company1.com',
            'is_admin' => false,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->regularUser2 = CrmUser::factory()->create([
            'name' => 'User Two',
            'email' => 'user2@company2.com',
            'is_admin' => false,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create a super admin user
        $this->superAdmin = CrmUser::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@system.com',
            'is_super_admin' => true,
            'is_admin' => true,
            'owner_company_id' => $this->company1->owner_company_id, // Primary company
        ]);

        // Assign roles to users
        $this->companyAdmin1->roles()->attach($this->adminRole);
        $this->companyAdmin2->roles()->attach($this->adminRole);
        $this->regularUser1->roles()->attach($this->userRole);
        $this->regularUser2->roles()->attach($this->userRole);
    }

    #[Test]
    public function users_are_isolated_between_companies()
    {
        // Verify that company 1 admin can only see company 1 users
        $this->actingAs($this->companyAdmin1);
        $response = $this->get(route('users.index'));
        $response->assertOk();
        $response->assertSee('Admin One');
        $response->assertSee('User One');
        $response->assertDontSee('Admin Two');
        $response->assertDontSee('User Two');

        // Verify that company 2 admin can only see company 2 users
        $this->actingAs($this->companyAdmin2);
        $response = $this->get(route('users.index'));
        $response->assertOk();
        $response->assertSee('Admin Two');
        $response->assertSee('User Two');
        $response->assertDontSee('Admin One');
        $response->assertDontSee('User One');

        // Verify that super admin can see all users
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('users.index'));
        $response->assertOk();
        $response->assertSee('Admin One');
        $response->assertSee('User One');
        $response->assertSee('Admin Two');
        $response->assertSee('User Two');
        $response->assertSee('Super Admin');
    }

    #[Test]
    public function company_admins_cannot_manage_users_from_other_companies()
    {
        // Try to view a user from another company
        $this->actingAs($this->companyAdmin1);
        $response = $this->get(route('users.show', $this->regularUser2->user_id));
        $response->assertForbidden();

        // Try to edit a user from another company
        $response = $this->get(route('users.edit', $this->regularUser2->user_id));
        $response->assertForbidden();

        // Try to update a user from another company
        $response = $this->put(route('users.update', $this->regularUser2->user_id), [
            'name' => 'Updated Name',
            'email' => 'updated@company2.com',
        ]);
        $response->assertForbidden();

        // Try to delete a user from another company
        $response = $this->delete(route('users.destroy', $this->regularUser2->user_id));
        $response->assertForbidden();

        // Verify that the user was not modified
        $this->assertDatabaseHas('crm_users', [
            'user_id' => $this->regularUser2->user_id,
            'name' => 'User Two',
            'email' => 'user2@company2.com',
        ]);
    }

    #[Test]
    public function super_admin_can_manage_users_from_all_companies()
    {
        // Super admin can view users from any company
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('users.show', $this->regularUser1->user_id));
        $response->assertOk();
        $response = $this->get(route('users.show', $this->regularUser2->user_id));
        $response->assertOk();

        // Super admin can edit users from any company
        $response = $this->get(route('users.edit', $this->regularUser2->user_id));
        $response->assertOk();

        // Super admin can update users from any company
        $response = $this->put(route('users.update', $this->regularUser2->user_id), [
            'name' => 'Updated User Two',
            'email' => 'updated.user2@company2.com',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
        $response->assertRedirect();

        // Verify that the user was modified
        $this->assertDatabaseHas('crm_users', [
            'user_id' => $this->regularUser2->user_id,
            'name' => 'Updated User Two',
            'email' => 'updated.user2@company2.com',
        ]);
    }

    #[Test]
    public function company_admins_can_create_users_only_for_their_company()
    {
        // Company 1 admin creates a user for company 1
        $this->actingAs($this->companyAdmin1);
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@company1.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'owner_company_id' => $this->company1->owner_company_id,
            'roles' => [$this->userRole->role_id],
        ];
        
        $response = $this->post(route('users.store'), $userData);
        $response->assertRedirect();
        
        // Verify that the user was created
        $this->assertDatabaseHas('crm_users', [
            'name' => 'New User',
            'email' => 'newuser@company1.com',
            'owner_company_id' => $this->company1->owner_company_id,
        ]);
        
        // Try to create a user for company 2
        $userData = [
            'name' => 'Another User',
            'email' => 'anotheruser@company2.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'owner_company_id' => $this->company2->owner_company_id, // Different company
            'roles' => [$this->userRole->role_id],
        ];
        
        $response = $this->post(route('users.store'), $userData);
        
        // The request should fail because the admin cannot create users for other companies
        $response->assertSessionHasErrors(['owner_company_id']);
        
        // Verify that no user was created
        $this->assertDatabaseMissing('crm_users', [
            'name' => 'Another User',
            'email' => 'anotheruser@company2.com',
        ]);
    }

    #[Test]
    public function super_admin_can_create_users_for_any_company()
    {
        // Super admin creates a user for company 2
        $this->actingAs($this->superAdmin);
        $userData = [
            'name' => 'Super Created User',
            'email' => 'supercreated@company2.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'owner_company_id' => $this->company2->owner_company_id,
            'roles' => [$this->userRole->role_id],
        ];
        
        $response = $this->post(route('users.store'), $userData);
        $response->assertRedirect();
        
        // Verify that the user was created
        $this->assertDatabaseHas('crm_users', [
            'name' => 'Super Created User',
            'email' => 'supercreated@company2.com',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }

    #[Test]
    public function company_admins_can_manage_roles_and_permissions_for_their_company_users()
    {
        // Create a new role
        $this->actingAs($this->companyAdmin1);
        $roleData = [
            'name' => 'Sales Representative',
            'description' => 'Sales team member',
            'permissions' => Permission::whereIn('name', [
                'view-customers',
                'create-customers',
                'edit-customers',
                'view-opportunities',
                'create-opportunities',
                'edit-opportunities',
                'view-quotations',
                'create-quotations',
                'edit-quotations'
            ])->pluck('permission_id')->toArray(),
        ];
        
        $response = $this->post(route('roles.store'), $roleData);
        $response->assertRedirect();
        
        // Verify that the role was created
        $this->assertDatabaseHas('roles', [
            'name' => 'Sales Representative',
        ]);
        
        // Get the created role
        $salesRole = Role::where('name', 'Sales Representative')->first();
        
        // Assign the role to a user in their company
        $response = $this->put(route('users.update', $this->regularUser1->user_id), [
            'name' => $this->regularUser1->name,
            'email' => $this->regularUser1->email,
            'owner_company_id' => $this->company1->owner_company_id,
            'roles' => [$salesRole->role_id],
        ]);
        $response->assertRedirect();
        
        // Verify that the role was assigned
        $this->assertTrue($this->regularUser1->fresh()->roles->contains($salesRole));
        
        // Try to assign the role to a user in another company
        $response = $this->put(route('users.update', $this->regularUser2->user_id), [
            'name' => $this->regularUser2->name,
            'email' => $this->regularUser2->email,
            'owner_company_id' => $this->company2->owner_company_id,
            'roles' => [$salesRole->role_id],
        ]);
        
        // The request should fail because the admin cannot manage users from other companies
        $response->assertForbidden();
    }

    #[Test]
    public function regular_users_cannot_access_user_management_features()
    {
        // Regular user tries to access user management
        $this->actingAs($this->regularUser1);
        
        // Try to view users list
        $response = $this->get(route('users.index'));
        $response->assertForbidden();
        
        // Try to create a user
        $response = $this->get(route('users.create'));
        $response->assertForbidden();
        
        // Try to view roles list
        $response = $this->get(route('roles.index'));
        $response->assertForbidden();
        
        // Try to create a role
        $response = $this->get(route('roles.create'));
        $response->assertForbidden();
    }

    #[Test]
    public function users_can_only_access_features_they_have_permission_for()
    {
        // Create a user with limited permissions
        $limitedUser = CrmUser::factory()->create([
            'name' => 'Limited User',
            'email' => 'limited@company1.com',
            'is_admin' => false,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);
        
        // Create a role with very limited permissions
        $limitedRole = Role::create([
            'name' => 'Limited Role',
            'description' => 'Role with very limited permissions',
        ]);
        
        // Assign only view-customers permission
        $viewCustomersPermission = Permission::where('name', 'view-customers')->first();
        $limitedRole->permissions()->attach($viewCustomersPermission);
        
        // Assign the role to the user
        $limitedUser->roles()->attach($limitedRole);
        
        // Log in as the limited user
        $this->actingAs($limitedUser);
        
        // Should be able to view customers
        $response = $this->get(route('customers.index'));
        $response->assertOk();
        
        // Should not be able to create customers
        $response = $this->get(route('customers.create'));
        $response->assertForbidden();
        
        // Should not be able to view products
        $response = $this->get(route('products.index'));
        $response->assertForbidden();
        
        // Should not be able to view invoices
        $response = $this->get(route('invoices.index'));
        $response->assertForbidden();
    }

    #[Test]
    public function company_switching_works_correctly_for_super_admin()
    {
        // Super admin can switch between companies
        $this->actingAs($this->superAdmin);
        
        // Initially viewing in the context of company 1
        $response = $this->get(route('dashboard', ['company' => $this->company1->owner_company_id]));
        $response->assertOk();
        $response->assertSee('Company One');
        
        // Switch to company 2
        $response = $this->get(route('dashboard', ['company' => $this->company2->owner_company_id]));
        $response->assertOk();
        $response->assertSee('Company Two');
        
        // Verify that the session has the correct company
        $this->assertEquals($this->company2->owner_company_id, session('current_company_id'));
    }

    #[Test]
    public function regular_users_cannot_switch_companies()
    {
        // Regular user tries to switch companies
        $this->actingAs($this->regularUser1);
        
        // Try to switch to company 2
        $response = $this->get(route('dashboard', ['company' => $this->company2->owner_company_id]));
        
        // Should be redirected back to their own company
        $response->assertRedirect();
        
        // Verify that the session still has their original company
        $this->assertEquals($this->company1->owner_company_id, session('current_company_id'));
    }
}