<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {
    }

    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->productRepository->paginate($filters, $perPage);
    }

    public function create(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->findOrFail($id);

        return $this->productRepository->update($product, $data);
    }

    public function delete(int $id): void
    {
        $product = $this->findOrFail($id);

        $this->productRepository->delete($product);
    }

    private function findOrFail(int $id): Product
    {
        $product = $this->productRepository->findById($id);

        if (! $product) {
            throw new ModelNotFoundException('Produto não encontrado.');
        }

        return $product;
    }
}
