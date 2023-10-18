<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\MotorbikeStoreRequest;
use App\Http\Requests\MotorbikeUpdateRequest;
use App\Http\Resources\MotorbikeEditResource;
use App\Http\Resources\ProductEditResource;
use App\Http\Resources\VendoProductResource;
use App\Models\Product;
use App\Models\SubCategory;
use App\Services\ProductService;
use App\Services\ProductStoreService;
use Illuminate\Http\Request;

class MotorbikeController extends Controller {
    protected $vendor_id, $shop_id;

    public function __construct() {
        $this->shop_id = auth('vendor')->user()?->shop?->id;
    }

    public function motorbikes(Request $request) {
        $products = Product::query()->select('id', 'name', 'images', 'sub_category_id', 'sku', 'quantity', 'selling_price', 'is_visible', 'status')
            ->where('shop_id', $this->shop_id)
            ->where('is_motorbike', 1)
            ->when($request->has('name'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->when($request->has('sku'), function ($query) use ($request) {
                $query->where('sku', 'like', '%' . $request->sku . '%');
            })
            ->when($request->has('category_id') && request('category_id') != 'ALL', function ($query) use ($request) {
                $ids = SubCategory::query()->where('category_id', $request->category_id)->pluck('id');
                $query->whereIn('sub_category_id', $ids);
            })
            ->when($request->has('subcategory_id'), function ($query) use ($request) {
                $query->whereIn('sub_category_id', $request->subcategory_id);
            });

        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        if ($products->count() < 1) {
            return $this->errorResponse($this->shop_id, 'Shop');
        }
        $products = $products->latest()->paginate($this->pagination);

        return $this->success(VendoProductResource::collection($products)->response()->getData(true));
    }

    public function motorbikeStore(MotorbikeStoreRequest $request, ProductStoreService $productStoreService){
        // dd($request->all());
        $product = $productStoreService->store($request->all(), $this->shop_id);

        return $this->success(new VendoProductResource($product));
    }

    public function motorbikeEdit($id){
        $product = Product::findOrFail($id);
        
        return $this->success(new MotorbikeEditResource($product));
    }

    public function motorbikeUpdate(MotorbikeUpdateRequest $request, $id, ProductStoreService $productStoreService){
        $product = Product::findOrFail($id);

        $product = $productStoreService->store($request->all(), $this->shop_id, $product);

        return $this->success(new VendoProductResource($product));
    }
}
