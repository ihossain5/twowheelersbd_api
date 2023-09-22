<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendoCategoryResource;
use App\Http\Resources\VendoProductResource;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\BrandModel;
use App\Models\Category;
use App\Models\Product;
use App\Models\Specification;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ProductController extends Controller {

    protected $vendor_id, $shop_id;

    public function __construct() {
        $this->shop_id = auth('vendor')->user()?->shop?->id;
        // $this->vendor_id = auth('vendor')->user()?->id;
    }

    public function categories() {
        return $this->success(VendoCategoryResource::collection(Category::query()->select('id', 'name')->get()));
    }

    public function subcategories($id) {
        return $this->success(SubCategory::query()->select('id', 'name')->where('category_id', $id)->get());
    }

    public function brands() {
        return $this->success(Brand::query()->select('id', 'name')->get());
    }

    public function brandModels($id) {
        $brand_category_ids = BrandCategory::query()->where('brand_id', $id)->pluck('id');

        return $this->success(BrandModel::query()->select('id', 'name')->whereIn('brand_category_id', $brand_category_ids)->get());
    }

    public function specifications() {
        return $this->success(Specification::query()->select('id', 'name')->get());
    }

    public function products(Request $request) {
        $products = Product::query()->select('id', 'name', 'images', 'sub_category_id', 'sku', 'quantity', 'selling_price', 'is_visible', 'status')
            ->where('shop_id', $this->shop_id)
            ->when(request('filter') == 'true', function ($q) use ($request) {
                $q->when($request->has('name'), function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                });

                $q->when($request->has('sku'), function ($query) use ($request) {
                    $query->where('sku', 'like', '%' . $request->sku . '%');
                });

                $q->when($request->has('category_id') && request('category_id') != 'ALL', function ($query) use ($request) {
                    $ids = SubCategory::query()->where('category_id', $request->category_id)->pluck('id');
                    $query->whereIn('sub_category_id', $ids);
                });

                $q->when($request->has('subcategory_id'), function ($query) use ($request) {
                    $query->whereIn('sub_category_id', $request->subcategory_id);
                });

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
