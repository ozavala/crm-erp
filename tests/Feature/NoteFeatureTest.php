<?php

namespace Tests\Feature;

use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\Note;
use App\Models\Opportunity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteFeatureTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected CrmUser $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = CrmUser::factory()->create();
        $this->actingAs($this->user, 'web');
        // Asignar permiso necesario para ver clientes y asociar notas
        // Esto es requerido por la lógica de autorización en notas y clientes
        $this->givePermission($this->user, 'view-customers');
    }

    #[Test]
    public function a_user_can_add_a_note_to_a_customer()
    {
        $customer = Customer::factory()->create();
        $noteBody = 'This is a test note for a customer.';

        $response = $this->post(route('notes.store'), [
            'body' => $noteBody,
            'noteable_id' => $customer->customer_id,
            'noteable_type' => 'Customer',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Note added successfully.');

        $this->assertDatabaseHas('notes', [
            'body' => $noteBody,
            'noteable_id' => $customer->customer_id,
            'noteable_type' => Customer::class,
            'created_by_user_id' => $this->user->user_id,
        ]);

        $this->assertCount(1, $customer->fresh()->notes);
        $this->assertEquals($noteBody, $customer->fresh()->notes->first()->body);
    }

    #[Test]
    public function a_user_can_add_a_note_to_an_opportunity()
    {
        $opportunity = Opportunity::factory()->create();
        $noteBody = 'This is a test note for an opportunity.';

        $response = $this->post(route('notes.store'), [
            'body' => $noteBody,
            'noteable_id' => $opportunity->opportunity_id,
            'noteable_type' => 'Opportunity',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notes', [
            'body' => $noteBody,
            'noteable_id' => $opportunity->opportunity_id,
            'noteable_type' => Opportunity::class,
        ]);

        $this->assertCount(1, $opportunity->fresh()->notes);
    }

    #[Test]
    public function a_user_can_delete_a_note()
    {
        $customer = Customer::factory()->create();
        $note = Note::factory()->create([
            'noteable_id' => $customer->customer_id,
            'noteable_type' => Customer::class,
        ]);

        $response = $this->delete(route('notes.destroy', $note));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Note deleted successfully.');
        $this->assertSoftDeleted('notes', ['note_id' => $note->note_id]);
    }

    #[Test]
    public function adding_a_note_is_visible_on_the_parent_entity_page()
    {
        $customer = Customer::factory()->create();
        $noteBody = $this->faker->sentence(10);

        // Debug: Check if user has the permission
        \Log::info("User has view-customers permission: " . ($this->user->hasPermissionTo('view-customers') ? 'YES' : 'NO'));
        \Log::info("User roles: " . $this->user->roles->pluck('name')->join(', '));
        
        $this->post(route('notes.store'), ['body' => $noteBody, 'noteable_id' => $customer->customer_id, 'noteable_type' => 'Customer']);

        $this->get(route('customers.show', $customer))
            ->assertOk()
            ->assertSeeText($noteBody)
            ->assertSeeText($this->user->full_name);
    }
}