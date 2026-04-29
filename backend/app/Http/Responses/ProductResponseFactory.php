<?php

namespace App\Http\Responses;

use App\Http\Resources\ProductResource;
use App\Http\Responses\Contracts\ProductResponseFactoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ProductResponseFactory implements ProductResponseFactoryInterface
{
    public function collection(LengthAwarePaginator $products): AnonymousResourceCollection
    {
        return ProductResource::collection($products);
    }

    public function created(Product $product): JsonResponse
    {
        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function item(Product $product): JsonResponse
    {
        return (new ProductResource($product))->response();
    }

    public function noContent(): Response
    {
        return response()->noContent();
    }
}
