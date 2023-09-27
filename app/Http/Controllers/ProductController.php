<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\HotDealProduct;
use App\Models\SubCategory;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller {
    public $productService;

    public function __construct(ProductService $productService) {
        $this->productService = $productService;
    }

    public function products(Request $request) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $products = $this->productService->getResults($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function productsByCategory(Request $request, $id) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $sub_category_ids = SubCategory::query()->where('category_id', $id)->pluck('id');

        $products = $this->productService->condition()->whereIn('sub_category_id', $sub_category_ids);

        if ($products->count() < 1) {
            return $this->errorResponse($id, 'Category');
        }

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function productsBySubCategory(Request $request, $id) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $products = $this->productService->condition()->where('sub_category_id', $id);

        if ($products->count() < 1) {
            return $this->errorResponse($id, 'SubCategory');
        }

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function productsByShop(Request $request, $id) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $products = $this->productService->condition()->where('shop_id', $id);

        if ($products->count() < 1) {
            return $this->errorResponse($id, 'Shop');
        }

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function productsByModel(Request $request, $id) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $products = $this->productService->condition()->where('brand_model_id', $id);

        if ($products->count() < 1) {
            return $this->errorResponse($id, 'Model');
        }

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function productsByHotDeal(Request $request, $id) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $product_ids = HotDealProduct::query()->where('hot_deal_id',$id)->pluck('product_id');

        $products = $this->productService->condition()->whereIn('id', $product_ids);

        if ($products->count() < 1) {
            return $this->errorResponse($id, 'Hot Deal');
        }

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }
}
