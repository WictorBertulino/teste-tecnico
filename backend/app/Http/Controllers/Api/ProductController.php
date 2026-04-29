<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Responses\Contracts\ProductResponseFactoryInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $productService,
        private readonly ProductResponseFactoryInterface $productResponseFactory,
    ) {
    }

    public function index(Request $request)
    {
        $products = $this->productService->list(
            filters: $request->only(['search', 'ativo']),
            perPage: (int) $request->integer('per_page', 10),
        );

        return $this->productResponseFactory->collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated());

        return $this->productResponseFactory->created($product);
    }

    public function update(UpdateProductRequest $request, int $product)
    {
        return $this->productResponseFactory->item(
            $this->productService->update($product, $request->validated()),
        );
    }

    public function destroy(int $product)
    {
        $this->productService->delete($product);

        return $this->productResponseFactory->noContent();
    }
}
