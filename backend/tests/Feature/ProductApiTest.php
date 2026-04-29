<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_products(): void
    {
        Product::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/products');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_it_creates_a_product(): void
    {
        $response = $this->postJson('/api/v1/products', [
            'nome' => 'Monitor UltraWide',
            'sku' => 'MONI-UW-34',
            'preco' => 2599.90,
            'estoque_atual' => 6,
            'estoque_minimo' => 2,
            'ativo' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.nome', 'Monitor UltraWide');

        $this->assertDatabaseHas('products', [
            'sku' => 'MONI-UW-34',
        ]);
    }

    public function test_it_updates_a_product(): void
    {
        $product = Product::factory()->create([
            'sku' => 'TABLET-001',
        ]);

        $response = $this->putJson("/api/v1/products/{$product->id}", [
            'nome' => 'Tablet Atualizado',
            'sku' => 'TABLET-001',
            'preco' => 1799.90,
            'estoque_atual' => 15,
            'estoque_minimo' => 4,
            'ativo' => false,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.ativo', false);
    }

    public function test_it_deletes_a_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/v1/products/{$product->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
