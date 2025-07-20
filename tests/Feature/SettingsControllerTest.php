<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\CrmUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SettingsTableSeeder::class);
        $this->user = CrmUser::factory()->create();
        $this->actingAs($this->user, 'web');
        // Asignar permisos necesarios para crear y editar settings
        // Esto es requerido por la lógica de autorización (Gate 'edit-settings')
        $this->givePermission($this->user, ['edit-settings', 'create-settings']);
    }

    public function test_edit_core_settings()
    {
        $response = $this->patch(route('settings.update'), [
            'company_name' => 'New Company Name',
        ]);
        $response->assertRedirect(route('settings.edit'));
        $this->assertEquals('New Company Name', Setting::where('key', 'company_name')->first()->value);
    }

    public function test_create_and_delete_custom_setting()
    {
        // Crear custom
        $response = $this->post(route('settings.custom.store'), [
            'key' => 'custom_field',
            'value' => 'Valor',
        ]);
        $response->assertRedirect(route('settings.edit'));
        $this->assertDatabaseHas('settings', ['key' => 'custom_field', 'type' => 'custom']);
        // Eliminar custom
        $setting = Setting::where('key', 'custom_field')->first();
        $response = $this->delete(route('settings.custom.destroy', $setting));
        $response->assertRedirect(route('settings.edit'));
        $this->assertDatabaseMissing('settings', ['key' => 'custom_field']);
    }

    public function test_cannot_delete_core_setting()
    {
        $core = Setting::where('key', 'company_name')->first();
        $response = $this->delete(route('settings.custom.destroy', $core));
        $response->assertRedirect(route('settings.edit'));
        $this->assertDatabaseHas('settings', ['key' => 'company_name']);
    }
} 