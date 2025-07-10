<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cuentas principales
        $accounts = [
            // Activos
            ['code' => '1101', 'name' => 'Banco', 'type' => 'Activo', 'description' => 'Cuenta bancaria principal', 'parent_id' => null],
            // Pasivos
            ['code' => '2101', 'name' => 'Proveedores', 'type' => 'Pasivo', 'description' => 'Cuentas por pagar a proveedores', 'parent_id' => null],
            ['code' => '2102', 'name' => 'Clientes', 'type' => 'Pasivo', 'description' => 'Cuentas por cobrar de clientes', 'parent_id' => null],
            // Ingresos
            ['code' => '3101', 'name' => 'Ventas', 'type' => 'Ingreso', 'description' => 'Ingresos por ventas de productos o servicios', 'parent_id' => null],
            ['code' => '3102', 'name' => 'Otros ingresos', 'type' => 'Ingreso', 'description' => 'Ingresos diversos no relacionados con ventas principales', 'parent_id' => null],
            // Costos y Gastos
            ['code' => '4101', 'name' => 'Compras', 'type' => 'Gasto', 'description' => 'Compras de mercancía', 'parent_id' => null],
            ['code' => '4102', 'name' => 'Costo de ventas', 'type' => 'Gasto', 'description' => 'Costo directo de los productos vendidos', 'parent_id' => null],
            ['code' => '4201', 'name' => 'Gastos administrativos', 'type' => 'Gasto', 'description' => 'Gastos generales de administración', 'parent_id' => null],
            ['code' => '4202', 'name' => 'Gastos de ventas', 'type' => 'Gasto', 'description' => 'Gastos relacionados con la venta', 'parent_id' => null],
            ['code' => '4203', 'name' => 'Gastos financieros', 'type' => 'Gasto', 'description' => 'Gastos por intereses y comisiones bancarias', 'parent_id' => null],
            // Impuestos
            ['code' => '5101', 'name' => 'IVA por pagar (ventas)', 'type' => 'Impuesto', 'description' => 'IVA generado por ventas', 'parent_id' => null],
            ['code' => '5102', 'name' => 'IVA por acreditar (compras)', 'type' => 'Impuesto', 'description' => 'IVA pagado en compras', 'parent_id' => null],
            ['code' => '5103', 'name' => 'Otros impuestos', 'type' => 'Impuesto', 'description' => 'Otros impuestos y tasas', 'parent_id' => null],
            // Patrimonio
            ['code' => '6101', 'name' => 'Capital social', 'type' => 'Patrimonio', 'description' => 'Aportaciones de los socios', 'parent_id' => null],
            ['code' => '6102', 'name' => 'Resultados acumulados', 'type' => 'Patrimonio', 'description' => 'Utilidades o pérdidas acumuladas', 'parent_id' => null],
        ];

        foreach ($accounts as $data) {
            Account::create($data);
        }
    }
} 