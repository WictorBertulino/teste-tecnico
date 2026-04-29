<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'nome' => 'Notebook Pro 14',
                'sku' => 'NOTE-14-PRO',
                'preco' => 7499.90,
                'estoque_atual' => 12,
                'estoque_minimo' => 4,
                'ativo' => true,
            ],
            [
                'nome' => 'Mouse Wireless MX',
                'sku' => 'MOUSE-MX-WL',
                'preco' => 299.90,
                'estoque_atual' => 37,
                'estoque_minimo' => 10,
                'ativo' => true,
            ],
            [
                'nome' => 'Teclado Mecânico TKL',
                'sku' => 'KEYB-TKL-MEC',
                'preco' => 459.00,
                'estoque_atual' => 8,
                'estoque_minimo' => 5,
                'ativo' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                ['uuid' => (string) Str::uuid(), ...$product],
            );
        }
    }
}
