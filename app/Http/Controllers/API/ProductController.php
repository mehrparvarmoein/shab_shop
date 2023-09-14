<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\ProductRepository;

class ProductController extends Controller
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function index(ProductIndexRequest $request)
    {
        return ProductResource::collection($this->productRepository->getProducts($request));
    }

    public function store(ProductRequest $request)
    {
        $this->productRepository->addProduct($request);
        
        return response()->json(['message' => __('product.create')]);
    }

    public function show(Product $product)
    {
        return  new ProductResource($product);
    }

    public function update(ProductRequest $request,Product $product)
    {
        $product = $this->productRepository->updateProduct($request,$product);
        
        return response()->json(['message' => __('product.update')]);
    }

    public function destroy(Product $product)
    {
        $product = $this->productRepository->deleteProduct($product);
        
        return response()->json(['message' => __('product.delete')]);
    }
}
