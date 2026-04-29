<?php

namespace App\Services\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Product;

    public function update(int $id, array $data): Product;

    public function delete(int $id): void;
}
