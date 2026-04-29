<?php

namespace App\Http\Responses\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

interface ProductResponseFactoryInterface
{
    public function collection(LengthAwarePaginator $products): AnonymousResourceCollection;

    public function created(Product $product): JsonResponse;

    public function item(Product $product): JsonResponse;

    public function noContent(): Response;
}
