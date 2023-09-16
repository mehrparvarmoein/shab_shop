<?php

namespace App\Repositories;

use App\Http\Requests\Product\ProductRequest;
use App\Jobs\ResizeImageJob;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRepository
{

    public function __construct(private UploadService $uploadService)
    {
    }

    public function getProducts(Request $request)
    {
        return Product::select('id', 'title', 'price')
            ->when($request->input('title'), function ($query) use ($request) {
                $query->where('title', 'like',"%" . $request->input('title') . "%");
            })
            ->when($request->input('shipping'), function ($query){
                $query->withShipping();
            })
            ->when(in_array($request->input('sort'),['title','price']), function ($query) use ($request) {
                $query->orderBy($request->input('sort'), $request->input('sort_order') ?? 'ASC');
            })
            ->paginate($request->input('per_page') ?? 20);
    }

    public function addProduct(ProductRequest $request)
    {
        DB::transaction(function () use($request){
            
            $product =  Product::create([
                'user_id'        => auth()->id(),
                'title'          => $request->input('title'),
                'price'          => $request->input('price'),
                'shipping_price' => $request->input('shipping_price'),
            ]);
    
            if ($request->has('images')) {
                foreach ($request->file('images') as $file) {
                    $this->uploadService->upload($product,$file,env('UPLOAD_DISK','public'));
                }

                ResizeImageJob::dispatch($product);
            }
        });

    }

    public function updateProduct(ProductRequest $request,Product $product)
    {
        return $product->update([
            'title'          => $request->input('title'),
            'price'          => $request->input('price'),
            'shipping_price' => $request->input('shipping_price'),
        ]);
    }

    public function deleteProduct(Product $product)
    {
        if (OrderItem::where('product_id',$product->id)->exists()) {
            abort(403,__('product.delete_constraint_order'));
        }
        if ($product->user_id != auth()->id()) {
            abort(403,__('product.delete_unauthorized'));
        }
        return $product->delete();
    }
}
