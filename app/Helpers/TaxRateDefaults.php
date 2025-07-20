<?php

namespace App\Helpers;

class TaxRateDefaults
{
    public static function getTaxRatesForCountry(string $countryCode): array
    {
        return match ($countryCode) {
            'ES' => [
                [
                    'name' => 'IVA General',
                    'rate' => 21.00,
                    'description' => 'Tasa general de IVA en España',
                    'product_type' => 'all',
                    'is_default' => true,
                ],
                [
                    'name' => 'IVA Reducido',
                    'rate' => 10.00,
                    'description' => 'Tasa reducida para productos básicos',
                    'product_type' => 'goods',
                ],
                [
                    'name' => 'IVA Superreducido',
                    'rate' => 4.00,
                    'description' => 'Tasa superreducida para productos de primera necesidad',
                    'product_type' => 'goods',
                ],
                [
                    'name' => 'IVA Cero',
                    'rate' => 0.00,
                    'description' => 'Productos exentos de IVA',
                    'product_type' => 'all',
                ],
            ],
            'MX' => [
                [
                    'name' => 'IVA General',
                    'rate' => 16.00,
                    'description' => 'Tasa general de IVA en México',
                    'product_type' => 'all',
                    'is_default' => true,
                ],
                [
                    'name' => 'IVA Cero',
                    'rate' => 0.00,
                    'description' => 'Productos exentos de IVA',
                    'product_type' => 'all',
                ],
            ],
            'AR' => [
                [
                    'name' => 'IVA General',
                    'rate' => 21.00,
                    'description' => 'Tasa general de IVA en Argentina',
                    'product_type' => 'all',
                    'is_default' => true,
                ],
                [
                    'name' => 'IVA Reducido',
                    'rate' => 10.50,
                    'description' => 'Tasa reducida para productos básicos',
                    'product_type' => 'goods',
                ],
                [
                    'name' => 'IVA Cero',
                    'rate' => 0.00,
                    'description' => 'Productos exentos de IVA',
                    'product_type' => 'all',
                ],
            ],
            'CO' => [
                [
                    'name' => 'IVA General',
                    'rate' => 19.00,
                    'description' => 'Tasa general de IVA en Colombia',
                    'product_type' => 'all',
                    'is_default' => true,
                ],
                [
                    'name' => 'IVA Cero',
                    'rate' => 0.00,
                    'description' => 'Productos exentos de IVA',
                    'product_type' => 'all',
                ],
            ],
            'EC' => [
                [
                    'name' => 'IVA 0% - Exento',
                    'rate' => 0.00,
                    'description' => 'Productos y servicios exentos de IVA en Ecuador',
                    'product_type' => 'all',
                ],
                [
                    'name' => 'IVA 15% - General',
                    'rate' => 15.00,
                    'description' => 'Tasa general de IVA en Ecuador',
                    'product_type' => 'all',
                ],
                [
                    'name' => 'IVA 22% - Especial',
                    'rate' => 22.00,
                    'description' => 'Tasa especial de IVA en Ecuador (bebidas alcohólicas, cigarrillos, etc.)',
                    'product_type' => 'all',
                ],
            ],
            default => [],
        };
    }
} 