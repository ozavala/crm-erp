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
        // Asumimos que hay un Observer o lógica en el modelo que previene la eliminación
        // de settings 'core'. El método delete() retornaría false.
        $core = Setting::create([
            'key' => 'company_name',
            'value' => 'Test Company',
            'type' => 'core',
            'is_editable' => false,
        ]);

        $this->assertDatabaseHas('settings', ['key' => 'company_name']);
        
        // La lógica de negocio debería prevenir la eliminación
        $this->assertFalse($core->delete(), "Core settings should not be deletable.");
        
        $this->assertDatabaseHas('settings', ['key' => 'company_name']);
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
        $this->assertTrue($custom->delete(), "Custom settings should be deletable.");
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