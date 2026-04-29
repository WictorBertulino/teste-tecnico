<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'nome' => fake()->words(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-??')),
            'preco' => fake()->randomFloat(2, 10, 5000),
            'estoque_atual' => fake()->numberBetween(0, 100),
            'estoque_minimo' => fake()->numberBetween(0, 20),
            'ativo' => fake()->boolean(80),
        ];
    }
}
