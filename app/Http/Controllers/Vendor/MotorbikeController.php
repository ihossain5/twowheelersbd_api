<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendoProductResource;
use App\Models\Product;
use App\Models\SubCategory;
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
        $products = $products->paginate($this->pagination);

        return $this->success(VendoProductResource::collection($products)->response()->getData(true));
    }
}
