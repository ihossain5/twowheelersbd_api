<?php

namespace App\Http\Controllers;

use App\Http\Resources\MotorbikeDetailsResource;
use App\Http\Resources\ProductResource;
use App\Models\BrandModel;
use App\Models\HotDealProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\SubCategory;
use App\Services\ProductService;
use Carbon\Carbon;
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

        $product_ids = HotDealProduct::query()->where('hot_deal_id', $id)->pluck('product_id');

        $products = $this->productService->condition()->whereIn('id', $product_ids);

        if ($products->count() < 1) {
            return $this->errorResponse($id, 'Hot Deal');
        }

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function shopWiseTopProducts(Request $request, $id) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $order_ids = Order::where('shop_id', $id)->pluck('id');

        $product_ids = OrderItem::whereIn('order_id', $order_ids)->pluck('product_id')->unique();

        $products = $this->productService->condition()->whereIn('id', $product_ids);

        if ($products->count() < 1) {
            return $this->errorResponse($id, 'Shop');
        }

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function shopWiseNewArraivalProducts(Request $request, $id) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $products = $this->productService->condition()->whereDate('created_at', '>=', Carbon::now()->subDays(7));

        if ($products->count() < 1) {
            return $this->errorResponse($id, 'Shop');
        }

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function getProductById($id) {
        $product = Product::query()
            ->select('id', 'sub_category_id', 'shop_id', 'additional_name_1', 'additional_name_2', 'additional_name_3', 'additional_name_4', 'additional_name_5', 'colors', 'description', 'video', 'sizes', 'catelogue_pdf', 'name', 'quantity', 'discount', 'regular_price', 'images', 'sku', 'selling_price', 'average_rating')
            ->withCount('reviews')
            ->with('subcategory:id,category_id,name', 'subcategory.category:id,name', 'shop:id,name', 'catelogues', 'specifications', 'motors', 'reviews')
            ->findOrFail($id);

        return $this->success(new ProductResource($product));
    }

    public function accessories(Request $request) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $subcategories = SubCategory::query()->where('category_id', 8)->pluck('id');

        $products = $this->productService->condition()->whereIn('sub_category_id', $subcategories);

        if ($products->count() < 1) {
            return $this->errorResponse('Category', 'accessories');
        }

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function motorbikes($id, Request $request) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $model = BrandModel::query()->where('id', $id)->pluck('id');

        $products = $this->productService->condition(1)->whereIn('brand_model_id', $model);

        if ($products->count() < 1) {
            return $this->errorResponse($id, 'Model');
        }

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function motorbikeDetails($id) {
        $motorbike = Product::query()
            ->select('id', 'sub_category_id', 'brand_model_id', 'shop_id', 'description', 'video', 'catelogue_pdf', 'name', 'quantity', 'discount', 'regular_price', 'images', 'sku', 'selling_price', 'average_rating', 'mileage')
            ->withCount('reviews')
            ->with('subcategory:id,category_id,name', 'subcategory.category:id,name', 'shop:id,name', 'catelogues', 'specifications', 'reviews', 'model:id', 'model.products')
            ->findOrFail($id);

        return $this->success(new MotorbikeDetailsResource($motorbike));
    }

    public function storeRating($id, Request $request) {
        $this->validate($request, ['rating' => 'required', 'review' => 'required']);

        $shop               = Product::findOrFail($id);
        $review             = new ProductReview();
        $review->product_id = $id;
        $review->user_id    = auth()->user()->id;
        $review->rating     = $request->rating;
        $review->review     = $request->review;
        $review->save();

        return $this->success('Review has been added successfully');
    }

    public function filterProducts(Request $request) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $products = $this->productService->condition()
            ->when($request->brand_id != null, function ($query) use ($request) {
                $query->where('brand_id', $request->brand_id);
            })
            ->when($request->model_id != null, function ($query) use ($request) {
                $query->where('brand_model_id', $request->model_id);
            })
            ->when($request->has('category_id') && request('category_id') != 'ALL', function ($query) use ($request) {
                $ids = SubCategory::query()->where('category_id', $request->category_id)->pluck('id');
                $query->whereIn('sub_category_id', $ids);
            })
            ->when($request->has('subcategory_id') && request('subcategory_id') != null, function ($query) use ($request) {
                $query->where('sub_category_id', $request->subcategory_id);
            })
            ->when($request->has('shop_id') && request('shop_id') != null, function ($query) use ($request) {
                $query->where('shop_id', $request->shop_id);
            })
            ->when($request->has('sort_by') && request('sort_by') == 'price_low', function ($query){
                $query->orderBy('selling_price', 'ASC');
            })
            ->when($request->has('sort_by') && request('sort_by') == 'price_high', function ($query){
                $query->orderBy('selling_price', 'DESC');
            });

        if ($products->count() < 1) {
            return $this->errorResponse('', 'Product');
        }

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function searchProducts(Request $request){
        $products = $this->productService->condition()
        ->where('name', 'like', '%'.$request->search.'%')
        ->orWhere('sku', 'like', '%'.$request->search.'%')
        ->orWhere('additional_name_1', 'like', '%'.$request->search.'%')
        ->orWhere('additional_name_2', 'like', '%'.$request->search.'%')
        ->orWhere('additional_name_3', 'like', '%'.$request->search.'%')
        ->orWhere('additional_name_4', 'like', '%'.$request->search.'%')
        ->orWhere('additional_name_5', 'like', '%'.$request->search.'%');

        $products = $products->latest()->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

}
