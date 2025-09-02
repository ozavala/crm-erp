<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Disable CSRF for tests
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        // El seeder de settings solo debe ejecutarse en tests funcionales que lo requieran
    }

    /**
     * Asigna uno o varios permisos a un usuario CrmUser para pruebas.
     * Si el permiso no existe, lo crea. Si el usuario no tiene un rol, se le asigna uno temporal.
     * El permiso se asigna al rol del usuario.
     *
     * @param \App\Models\CrmUser $user
     * @param string|array $permissions Nombre(s) del permiso a asignar (ej: 'edit-settings')
     */
    protected function givePermission($user, $permissions)
    {
        $permissions = (array) $permissions;
        $role = $user->roles()->first();
        if (!$role) {
            // Crear un rol temporal si el usuario no tiene ninguno
            $role = \App\Models\UserRole::create([
                'name' => 'TestRole_' . uniqid(),
                'description' => 'Rol temporal para testing',
            ]);
            $user->roles()->attach($role);
        }
        foreach ($permissions as $permName) {
            $permission = \App\Models\Permission::firstOrCreate([
                'name' => $permName
            ], [
                'description' => 'Permiso temporal para testing'
            ]);
            // Asignar el permiso al rol si no lo tiene
            if (!$role->permissions()->where('name', $permName)->exists()) {
                $role->permissions()->attach($permission);
            }
        }
        // Refrescar relaciones
        $user->load('roles.permissions');
    }
}
