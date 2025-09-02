<?php

namespace Tests\Feature;

use App\Models\CrmUser;
use App\Models\InventoryMovement;
use App\Models\OwnerCompany;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockLevel;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventoryMultiCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected OwnerCompany $company1;
    protected OwnerCompany $company2;
    protected CrmUser $user1;
    protected CrmUser $user2;
    protected CrmUser $superAdmin;
    protected Warehouse $warehouse1;
    protected Warehouse $warehouse2;
    protected ProductCategory $category1;
    protected ProductCategory $category2;

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
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'view-inventory',
            'manage-inventory',
            'view-warehouses',
            'create-warehouses',
            'edit-warehouses',
            'delete-warehouses',
            'view-product-categories',
            'create-product-categories',
            'edit-product-categories',
            'delete-product-categories'
        ]);

        $this->givePermission($this->user2, [
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'view-inventory',
            'manage-inventory',
            'view-warehouses',
            'create-warehouses',
            'edit-warehouses',
            'delete-warehouses',
            'view-product-categories',
            'create-product-categories',
            'edit-product-categories',
            'delete-product-categories'
        ]);

        $this->givePermission($this->superAdmin, [
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'view-inventory',
            'manage-inventory',
            'view-warehouses',
            'create-warehouses',
            'edit-warehouses',
            'delete-warehouses',
            'view-product-categories',
            'create-product-categories',
            'edit-product-categories',
            'delete-product-categories',
            'manage-companies'
        ]);

        // Create warehouses for each company
        $this->warehouse1 = Warehouse::create([
            'name' => 'Warehouse 1',
            'location' => 'Location 1',
            'description' => 'Main warehouse for Company 1',
            'is_active' => true,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->warehouse2 = Warehouse::create([
            'name' => 'Warehouse 2',
            'location' => 'Location 2',
            'description' => 'Main warehouse for Company 2',
            'is_active' => true,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create product categories for each company
        $this->category1 = ProductCategory::create([
            'name' => 'Category 1',
            'description' => 'Product category for Company 1',
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $this->category2 = ProductCategory::create([
            'name' => 'Category 2',
            'description' => 'Product category for Company 2',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }

    #[Test]
    public function products_are_isolated_between_companies()
    {
        // Create products for company 1
        $this->actingAs($this->user1);
        $product1 = Product::create([
            'name' => 'Product 1',
            'description' => 'Description for Product 1',
            'sku' => 'SKU-001-C1',
            'barcode' => 'BARCODE-001-C1',
            'price' => 100.00,
            'cost' => 50.00,
            'category_id' => $this->category1->category_id,
            'is_active' => true,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create products for company 2
        $this->actingAs($this->user2);
        $product2 = Product::create([
            'name' => 'Product 2',
            'description' => 'Description for Product 2',
            'sku' => 'SKU-001-C2',
            'barcode' => 'BARCODE-001-C2',
            'price' => 200.00,
            'cost' => 100.00,
            'category_id' => $this->category2->category_id,
            'is_active' => true,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Verify that company 1 user can only see company 1 products
        $this->actingAs($this->user1);
        $response = $this->get(route('products.index'));
        $response->assertOk();
        $response->assertSee('Product 1');
        $response->assertDontSee('Product 2');

        // Verify that company 2 user can only see company 2 products
        $this->actingAs($this->user2);
        $response = $this->get(route('products.index'));
        $response->assertOk();
        $response->assertSee('Product 2');
        $response->assertDontSee('Product 1');

        // Verify that super admin can see both companies' products
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('products.index'));
        $response->assertOk();
        $response->assertSee('Product 1');
        $response->assertSee('Product 2');
    }

    #[Test]
    public function warehouses_are_isolated_between_companies()
    {
        // Verify that company 1 user can only see company 1 warehouses
        $this->actingAs($this->user1);
        $response = $this->get(route('warehouses.index'));
        $response->assertOk();
        $response->assertSee('Warehouse 1');
        $response->assertDontSee('Warehouse 2');

        // Verify that company 2 user can only see company 2 warehouses
        $this->actingAs($this->user2);
        $response = $this->get(route('warehouses.index'));
        $response->assertOk();
        $response->assertSee('Warehouse 2');
        $response->assertDontSee('Warehouse 1');

        // Verify that super admin can see both companies' warehouses
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('warehouses.index'));
        $response->assertOk();
        $response->assertSee('Warehouse 1');
        $response->assertSee('Warehouse 2');
    }

    #[Test]
    public function product_categories_are_isolated_between_companies()
    {
        // Verify that company 1 user can only see company 1 product categories
        $this->actingAs($this->user1);
        $response = $this->get(route('product-categories.index'));
        $response->assertOk();
        $response->assertSee('Category 1');
        $response->assertDontSee('Category 2');

        // Verify that company 2 user can only see company 2 product categories
        $this->actingAs($this->user2);
        $response = $this->get(route('product-categories.index'));
        $response->assertOk();
        $response->assertSee('Category 2');
        $response->assertDontSee('Category 1');

        // Verify that super admin can see both companies' product categories
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('product-categories.index'));
        $response->assertOk();
        $response->assertSee('Category 1');
        $response->assertSee('Category 2');
    }

    #[Test]
    public function inventory_movements_are_isolated_between_companies()
    {
        // Create products for both companies
        $product1 = Product::create([
            'name' => 'Product 1',
            'description' => 'Description for Product 1',
            'sku' => 'SKU-001-C1',
            'barcode' => 'BARCODE-001-C1',
            'price' => 100.00,
            'cost' => 50.00,
            'category_id' => $this->category1->category_id,
            'is_active' => true,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $product2 = Product::create([
            'name' => 'Product 2',
            'description' => 'Description for Product 2',
            'sku' => 'SKU-001-C2',
            'barcode' => 'BARCODE-001-C2',
            'price' => 200.00,
            'cost' => 100.00,
            'category_id' => $this->category2->category_id,
            'is_active' => true,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create inventory movements for company 1
        $this->actingAs($this->user1);
        $movement1 = InventoryMovement::create([
            'product_id' => $product1->product_id,
            'warehouse_id' => $this->warehouse1->warehouse_id,
            'movement_type' => 'in',
            'quantity' => 100,
            'reference' => 'REF-001-C1',
            'notes' => 'Initial stock for Product 1',
            'created_by_user_id' => $this->user1->user_id,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create inventory movements for company 2
        $this->actingAs($this->user2);
        $movement2 = InventoryMovement::create([
            'product_id' => $product2->product_id,
            'warehouse_id' => $this->warehouse2->warehouse_id,
            'movement_type' => 'in',
            'quantity' => 200,
            'reference' => 'REF-001-C2',
            'notes' => 'Initial stock for Product 2',
            'created_by_user_id' => $this->user2->user_id,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Verify that company 1 user can only see company 1 inventory movements
        $this->actingAs($this->user1);
        $response = $this->get(route('inventory-movements.index'));
        $response->assertOk();
        $response->assertSee('REF-001-C1');
        $response->assertDontSee('REF-001-C2');

        // Verify that company 2 user can only see company 2 inventory movements
        $this->actingAs($this->user2);
        $response = $this->get(route('inventory-movements.index'));
        $response->assertOk();
        $response->assertSee('REF-001-C2');
        $response->assertDontSee('REF-001-C1');

        // Verify that super admin can see both companies' inventory movements
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('inventory-movements.index'));
        $response->assertOk();
        $response->assertSee('REF-001-C1');
        $response->assertSee('REF-001-C2');
    }

    #[Test]
    public function stock_levels_are_isolated_between_companies()
    {
        // Create products for both companies
        $product1 = Product::create([
            'name' => 'Product 1',
            'description' => 'Description for Product 1',
            'sku' => 'SKU-001-C1',
            'barcode' => 'BARCODE-001-C1',
            'price' => 100.00,
            'cost' => 50.00,
            'category_id' => $this->category1->category_id,
            'is_active' => true,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $product2 = Product::create([
            'name' => 'Product 2',
            'description' => 'Description for Product 2',
            'sku' => 'SKU-001-C2',
            'barcode' => 'BARCODE-001-C2',
            'price' => 200.00,
            'cost' => 100.00,
            'category_id' => $this->category2->category_id,
            'is_active' => true,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Create stock levels for company 1
        $this->actingAs($this->user1);
        $stockLevel1 = StockLevel::create([
            'product_id' => $product1->product_id,
            'warehouse_id' => $this->warehouse1->warehouse_id,
            'quantity' => 100,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Create stock levels for company 2
        $this->actingAs($this->user2);
        $stockLevel2 = StockLevel::create([
            'product_id' => $product2->product_id,
            'warehouse_id' => $this->warehouse2->warehouse_id,
            'quantity' => 200,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Verify that company 1 user can only see company 1 stock levels
        $this->actingAs($this->user1);
        $response = $this->get(route('stock-levels.index'));
        $response->assertOk();
        $response->assertSee('Product 1');
        $response->assertSee('100'); // Stock level for Product 1
        $response->assertDontSee('Product 2');
        $response->assertDontSee('200'); // Stock level for Product 2

        // Verify that company 2 user can only see company 2 stock levels
        $this->actingAs($this->user2);
        $response = $this->get(route('stock-levels.index'));
        $response->assertOk();
        $response->assertSee('Product 2');
        $response->assertSee('200'); // Stock level for Product 2
        $response->assertDontSee('Product 1');
        $response->assertDontSee('100'); // Stock level for Product 1

        // Verify that super admin can see both companies' stock levels
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('stock-levels.index'));
        $response->assertOk();
        $response->assertSee('Product 1');
        $response->assertSee('100'); // Stock level for Product 1
        $response->assertSee('Product 2');
        $response->assertSee('200'); // Stock level for Product 2
    }

    #[Test]
    public function users_cannot_create_inventory_entities_for_other_companies()
    {
        // Create a product for company 1
        $product1 = Product::create([
            'name' => 'Product 1',
            'description' => 'Description for Product 1',
            'sku' => 'SKU-001-C1',
            'barcode' => 'BARCODE-001-C1',
            'price' => 100.00,
            'cost' => 50.00,
            'category_id' => $this->category1->category_id,
            'is_active' => true,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        // Try to create an inventory movement for company 2 as company 1 user
        $this->actingAs($this->user1);
        
        $movementData = [
            'product_id' => $product1->product_id,
            'warehouse_id' => $this->warehouse2->warehouse_id, // Company 2 warehouse
            'movement_type' => 'in',
            'quantity' => 100,
            'reference' => 'REF-002-C1',
            'notes' => 'Trying to add stock to Company 2 warehouse',
            'owner_company_id' => $this->company2->owner_company_id, // Trying to set company 2
        ];
        
        $response = $this->post(route('inventory-movements.store'), $movementData);
        
        // The request should fail because the user cannot create inventory movements for other companies
        $response->assertSessionHasErrors(['warehouse_id', 'owner_company_id']);
        
        // Verify that no inventory movement was created
        $this->assertDatabaseMissing('inventory_movements', [
            'reference' => 'REF-002-C1',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }

    #[Test]
    public function super_admin_can_create_inventory_entities_for_any_company()
    {
        // Create products for both companies
        $product1 = Product::create([
            'name' => 'Product 1',
            'description' => 'Description for Product 1',
            'sku' => 'SKU-001-C1',
            'barcode' => 'BARCODE-001-C1',
            'price' => 100.00,
            'cost' => 50.00,
            'category_id' => $this->category1->category_id,
            'is_active' => true,
            'owner_company_id' => $this->company1->owner_company_id,
        ]);

        $product2 = Product::create([
            'name' => 'Product 2',
            'description' => 'Description for Product 2',
            'sku' => 'SKU-001-C2',
            'barcode' => 'BARCODE-001-C2',
            'price' => 200.00,
            'cost' => 100.00,
            'category_id' => $this->category2->category_id,
            'is_active' => true,
            'owner_company_id' => $this->company2->owner_company_id,
        ]);

        // Super admin should be able to create inventory movements for any company
        $this->actingAs($this->superAdmin);
        
        // Create inventory movement for company 2
        $movementData = [
            'product_id' => $product2->product_id,
            'warehouse_id' => $this->warehouse2->warehouse_id,
            'movement_type' => 'in',
            'quantity' => 100,
            'reference' => 'REF-ADMIN-C2',
            'notes' => 'Admin adding stock to Company 2 product',
            'owner_company_id' => $this->company2->owner_company_id,
        ];
        
        $response = $this->post(route('inventory-movements.store'), $movementData);
        $response->assertRedirect();
        
        // Verify that the inventory movement was created
        $this->assertDatabaseHas('inventory_movements', [
            'reference' => 'REF-ADMIN-C2',
            'owner_company_id' => $this->company2->owner_company_id,
        ]);
    }
}