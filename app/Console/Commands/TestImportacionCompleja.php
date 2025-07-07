<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TaxCalculationService;

class TestImportacionCompleja extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:importacion-compleja';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test del caso complejo de importación con todos los costos e impuestos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚢 Probando Caso Complejo de Importación...');
        $this->info('==============================================');
        
        // Datos base
        $valorMercaderia = 300000.00; // 6 computadoras de $50,000 c/u
        $this->info("📦 Valor de la Mercadería: $" . number_format($valorMercaderia, 2));
        
        // 1. Costos bancarios (1.2% + IVA 22%)
        $costoBancario = $valorMercaderia * 0.012;
        $ivaBancario = $costoBancario * 0.22;
        $totalBancario = $costoBancario + $ivaBancario;
        
        $this->info("\n🏦 COSTOS BANCARIOS:");
        $this->line("   - Comisión bancaria (1.2%): $" . number_format($costoBancario, 2));
        $this->line("   - IVA bancario (22%): $" . number_format($ivaBancario, 2));
        $this->line("   - Total bancario: $" . number_format($totalBancario, 2));
        
        // 2. ISD 5% sobre transferencia
        $isd = $valorMercaderia * 0.05;
        $this->info("\n💰 IMPUESTO A LA SALIDA DE DIVISAS (ISD):");
        $this->line("   - ISD (5%): $" . number_format($isd, 2));
        
        // 3. Transporte aéreo (IVA 15%)
        $transporteAereo = 4560.00;
        $ivaTransporte = $transporteAereo * 0.15;
        $totalTransporte = $transporteAereo + $ivaTransporte;
        
        $this->info("\n✈️ TRANSPORTE AÉREO:");
        $this->line("   - Flete aéreo: $" . number_format($transporteAereo, 2));
        $this->line("   - IVA transporte (15%): $" . number_format($ivaTransporte, 2));
        $this->line("   - Total transporte: $" . number_format($totalTransporte, 2));
        
        // 4. Seguro (1.5% del valor + flete, IVA 22%)
        $baseSeguro = $valorMercaderia + $transporteAereo;
        $seguro = $baseSeguro * 0.015;
        $ivaSeguro = $seguro * 0.22;
        $totalSeguro = $seguro + $ivaSeguro;
        
        $this->info("\n🛡️ SEGURO:");
        $this->line("   - Base del seguro (mercadería + flete): $" . number_format($baseSeguro, 2));
        $this->line("   - Prima de seguro (1.5%): $" . number_format($seguro, 2));
        $this->line("   - IVA seguro (22%): $" . number_format($ivaSeguro, 2));
        $this->line("   - Total seguro: $" . number_format($totalSeguro, 2));
        
        // 5. Arancel 5% sobre base imponible
        $baseArancel = $valorMercaderia + $transporteAereo + $seguro;
        $arancel = $baseArancel * 0.05;
        
        $this->info("\n📋 ARANCEL:");
        $this->line("   - Base arancelaria (mercadería + flete + seguro): $" . number_format($baseArancel, 2));
        $this->line("   - Arancel (5%): $" . number_format($arancel, 2));
        
        // 6. Bodegaje (IVA 22%)
        $bodegaje = 120.00;
        $ivaBodegaje = $bodegaje * 0.22;
        $totalBodegaje = $bodegaje + $ivaBodegaje;
        
        $this->info("\n🏭 BODEGAJE:");
        $this->line("   - Bodegaje (1 mes): $" . number_format($bodegaje, 2));
        $this->line("   - IVA bodegaje (22%): $" . number_format($ivaBodegaje, 2));
        $this->line("   - Total bodegaje: $" . number_format($totalBodegaje, 2));
        
        // 7. Agencia (IVA 22%)
        $agencia = 300.00;
        $ivaAgencia = $agencia * 0.22;
        $totalAgencia = $agencia + $ivaAgencia;
        
        $this->info("\n🏢 AGENCIA:");
        $this->line("   - Servicios de agencia: $" . number_format($agencia, 2));
        $this->line("   - IVA agencia (22%): $" . number_format($ivaAgencia, 2));
        $this->line("   - Total agencia: $" . number_format($totalAgencia, 2));
        
        // 8. FODINFA 0.05%
        $baseFodinfa = $valorMercaderia + $transporteAereo + $arancel;
        $fodinfa = $baseFodinfa * 0.0005;
        
        $this->info("\n🏛️ FODINFA:");
        $this->line("   - Base FODINFA (mercadería + flete + arancel): $" . number_format($baseFodinfa, 2));
        $this->line("   - FODINFA (0.05%): $" . number_format($fodinfa, 2));
        
        // 9. Transporte interno (exento)
        $transporteInterno = 300.00;
        
        $this->info("\n🚛 TRANSPORTE INTERNO:");
        $this->line("   - Transporte interno (exento de IVA): $" . number_format($transporteInterno, 2));
        
        // Totales
        $totalCostosImponibles = $costoBancario + $transporteAereo + $seguro + $bodegaje + $agencia;
        $totalIva = $ivaBancario + $ivaTransporte + $ivaSeguro + $ivaBodegaje + $ivaAgencia;
        $totalCostosExentos = $isd + $arancel + $fodinfa + $transporteInterno;
        
        $costoTotal = $valorMercaderia + $totalCostosImponibles + $totalIva + $totalCostosExentos;
        
        $this->info("\n📊 RESUMEN DE COSTOS:");
        $this->line("   - Costos imponibles: $" . number_format($totalCostosImponibles, 2));
        $this->line("   - IVA total: $" . number_format($totalIva, 2));
        $this->line("   - Costos exentos: $" . number_format($totalCostosExentos, 2));
        
        $this->info("\n💰 COSTO TOTAL DE IMPORTACIÓN:");
        $this->line("   - Valor mercadería: $" . number_format($valorMercaderia, 2));
        $this->line("   - Costos adicionales: $" . number_format($totalCostosImponibles + $totalIva + $totalCostosExentos, 2));
        $this->line("   - TOTAL: $" . number_format($costoTotal, 2));
        
        $porcentajeAdicional = (($costoTotal - $valorMercaderia) / $valorMercaderia) * 100;
        $this->info("\n📈 ANÁLISIS:");
        $this->line("   - Costo adicional: $" . number_format($costoTotal - $valorMercaderia, 2));
        $this->line("   - Porcentaje adicional: " . number_format($porcentajeAdicional, 2) . "%");
        
        // Test con el servicio de cálculo
        $this->testConServicio($valorMercaderia);
    }
    
    private function testConServicio($valorMercaderia)
    {
        $this->info("\n🧪 PRUEBA CON SERVICIO DE CÁLCULO:");
        
        $taxService = new TaxCalculationService();
        
        $costos = [
            ['category' => 'services', 'amount' => 3600], // Comisión bancaria
            ['category' => 'transport', 'amount' => 4560], // Transporte aéreo
            ['category' => 'insurance', 'amount' => 4569], // Seguro
            ['category' => 'storage', 'amount' => 120], // Bodegaje
            ['category' => 'services', 'amount' => 300], // Agencia
            ['category' => 'transport_public', 'amount' => 300], // Transporte interno (exento)
        ];
        
        $resultado = $taxService->calculateAdditionalCostsTax($costos, 'EC');
        
        $this->line("   - Costos totales: $" . number_format($resultado['total_amount'], 2));
        $this->line("   - IVA calculado: $" . number_format($resultado['total_tax'], 2));
        $this->line("   - Total con IVA: $" . number_format($resultado['total_with_tax'], 2));
        
        // Verificar que el transporte público no paga IVA
        foreach ($resultado['costs'] as $costo) {
            if ($costo['category'] === 'transport_public') {
                $this->line("   - ✅ Transporte público: IVA 0% (exento)");
            }
        }
    }
}
