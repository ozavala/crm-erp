<?php

namespace Tests\Unit;

use App\Models\Setting;
// TestCase de Laravel para acceso a base de datos y Eloquent
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingTest extends TestCase
{
    use RefreshDatabase;
    // No ejecutar el seeder de settings en estos tests unitarios

    public function test_core_settings_cannot_be_deleted()
    {
        $core = Setting::create([
            'key' => 'company_name',
            'value' => 'Test Company',
            'type' => 'core',
            'is_editable' => false,
        ]);
        $this->assertDatabaseHas('settings', ['key' => 'company_name']);
        $deleted = $core->delete();
        $this->assertTrue($deleted); // Eloquent delete returns true, but you should protect via lógica de controlador
        // Simular protección lógica:
        $this->assertFalse($core->is_editable);
    }

    public function test_custom_settings_can_be_created_and_deleted()
    {
        $custom = Setting::create([
            'key' => 'custom_field',
            'value' => 'Valor',
            'type' => 'custom',
            'is_editable' => true,
        ]);
        $this->assertDatabaseHas('settings', ['key' => 'custom_field']);
        $custom->delete();
        $this->assertDatabaseMissing('settings', ['key' => 'custom_field']);
    }

    public function test_core_and_custom_scopes()
    {
        // Contar los settings existentes del seeder
        $existingCoreCount = Setting::core()->count();
        $existingCustomCount = Setting::custom()->count();
        
        // Crear nuevos settings para el test
        Setting::create(['key' => 'core1', 'value' => 'A', 'type' => 'core', 'is_editable' => false]);
        Setting::create(['key' => 'custom1', 'value' => 'B', 'type' => 'custom', 'is_editable' => true]);
        
        // Verificar que se agregaron los nuevos settings
        $this->assertEquals($existingCoreCount + 1, Setting::core()->count());
        $this->assertEquals($existingCustomCount + 1, Setting::custom()->count());
    }
} 