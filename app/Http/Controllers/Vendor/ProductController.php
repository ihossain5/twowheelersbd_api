<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductEditResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\VendoCategoryResource;
use App\Http\Resources\VendoProductResource;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\BrandModel;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCatelogue;
use App\Models\ProductMotor;
use App\Models\ProductReview;
use App\Models\ProductSpecification;
use App\Models\Specification;
use App\Models\SubCategory;
use App\Services\ImageUoloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function productReviews($id) {
        $reviews = ProductReview::query()->where('product_id', $id)->get();

        if ($reviews->count() < 1) {
            return $this->errorResponse($id, 'Product');
        }
        return $this->success(ReviewResource::collection($reviews));
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
        ->withCount('all_reviews')
            ->where('shop_id', $this->shop_id)
            ->where('is_motorbike', 0)
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%');
            })
            // ->when($request->has('sku'), function ($query) use ($request) {
            //     $query->where('sku', 'like', '%' . $request->sku . '%');
            // })
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

    public function productStore(ProductStoreRequest $request) {
        // dd($request->all());
        $product = new Product();

        DB::transaction(function () use ($request, $product) {

            $product = $this->saveCommonData($product, $request);

            if ($request->catelogue_pdf) {
                $product->catelogue_pdf = (new ImageUoloadService())->storeFile($request->catelogue_pdf, 'product/catelogue-pdf/');
            }

            if ($request->video) {
                $product->video = (new ImageUoloadService())->storeFile($request->video, 'product/video/');
            }

            $image_arr = [];
            foreach ($request->images as $key => $image) {
                $image_arr[$key] = (new ImageUoloadService())->storeImage($image, 'product/images/', 568, 570);
            }
            $product->images = json_encode($image_arr);

            $product->save();

            $product->slug = Str::slug($request->name . ' ' . $product->id);
            $product->save();

            $catelogues = $request->catelogues;

            if ($catelogues) {
                foreach ($catelogues as $catelogue) {
                    if (array_key_exists('image', $catelogue)) {
                        $product_catelogue             = new ProductCatelogue();
                        $product_catelogue->product_id = $product->id;
                        $product_catelogue->sku        = $catelogue['sku'];
                        $product_catelogue->title      = $catelogue['title'];
                        $product_catelogue->image      = (new ImageUoloadService())->storeImage($catelogue['image'], 'product/catelogues/', 374, 260);
                        $product_catelogue->save();
                    }

                }
            }

            if ($request->specifications) {
                foreach ($request->specifications as $specification) {
                    if ($specification['value'] != null) {
                        $product_specification                   = new ProductSpecification();
                        $product_specification->product_id       = $product->id;
                        $product_specification->specification_id = $specification['specification_id'];
                        $product_specification->value            = $specification['value'];
                        $product_specification->save();
                    }
                }
            }

            if ($request->motors) {
                foreach ($request->motors as $motor) {
                    if (array_key_exists('model_id', $motor) && $motor['model_id'] != null) {
                        $product_motor                 = new ProductMotor();
                        $product_motor->product_id     = $product->id;
                        $product_motor->brand_model_id = $motor['model_id'];
                        $product_motor->save();
                    }
                }
            }

        });

        return $this->success(new VendoProductResource($product));
    }

    private function array_null_remove($arr_data) {
        $new_array = [];
        foreach ($arr_data as $data) {
            if ($data !== null) {
                $new_array[] = $data;
            }
        }

        if (!empty($new_array)) {
            ksort($new_array);
            $new_array = array_combine(range(1, count($new_array)), array_values($new_array));
        }

        return json_encode($new_array);
    }

    public function productEdit($id) {
        $product = Product::findOrFail($id);
        return $this->success(new ProductEditResource($product));
    }

    public function productUpdate(ProductUpdateRequest $request, $id) {
        $product = Product::findOrFail($id);

        DB::transaction(function () use ($request, $product) {
            $product = $this->saveCommonData($product, $request);

            if ($request->catelogue_pdf) {
                if ($product->catelogue_pdf) {
                    (new ImageUoloadService())->deleteFile($product->catelogue_pdf);
                }

                $product->catelogue_pdf = (new ImageUoloadService())->storeFile($request->catelogue_pdf, 'product/catelogue-pdf/');
            }

            if ($request->video) {
                if ($product->video) {
                    (new ImageUoloadService())->deleteFile($product->video);
                }

                $product->video = (new ImageUoloadService())->storeFile($request->video, 'product/video/');
            }

            $old_photos = collect(json_decode($product->images))->toArray();
            if ($request->images) {
                foreach ($request->images as $key => $photo) {
                    if (array_key_exists($key, $old_photos)) {
                        (new ImageUoloadService())->deleteImage($old_photos[$key]);
                        unset($old_photos[$key]);
                    }
                    $old_photos[$key] = (new ImageUoloadService())->storeImage($photo, 'product/images/', 568, 570);
                }
            }

            ksort($old_photos);
            $old_photos = array_combine(range(1, count($old_photos)), array_values($old_photos));

            $product->images = json_encode($old_photos);

            $product->save();

            //existing catelogue update
            if ($request->old_catelogues) {
                foreach ($request->old_catelogues as $old_catelogue) {
                    if ($old_catelogue['title'] !== null) {
                        $find_catelogue        = ProductCatelogue::find($old_catelogue['id']);
                        $find_catelogue->title = $old_catelogue['title'];
                        $find_catelogue->sku   = $old_catelogue['sku'];

                        if (array_key_exists('image', $old_catelogue)) {
                            (new ImageUoloadService())->deleteImage($find_catelogue->image);

                            $find_catelogue->image = (new ImageUoloadService())->storeImage($old_catelogue['image'], 'product/catelogues/', 374, 260);
                        }
                        $find_catelogue->save();
                    }
                }
            }

            // new catelogue create
            if ($request->catelogues) {
                $this->catelogueStore($request->catelogues, $product->id);
            }

            if ($request->specifications) {
                foreach ($request->specifications as $specification) {
                    $exists = ProductSpecification::where('product_id', $product->id)->where('specification_id', $specification['specification_id'])->first();
                    if ($specification['value'] != null) {
                        if ($exists) {
                            $exists->value = $specification['value'];
                            $exists->save();
                        } else {
                            $brand_specification                   = new ProductSpecification();
                            $brand_specification->product_id       = $product->id;
                            $brand_specification->specification_id = $specification['specification_id'];
                            $brand_specification->value            = $specification['value'];
                            $brand_specification->save();
                        }
                    } else {
                        if ($exists) {
                            $exists->delete();
                        }
                    }
                }
            }

            if ($request->motors) {
                foreach ($request->motors as $motor) {
                    if (array_key_exists('model_id', $motor) && $motor['model_id'] != null) {
                        $product_motor                 = new ProductMotor();
                        $product_motor->product_id     = $product->id;
                        $product_motor->brand_model_id = $motor['model_id'];
                        $product_motor->save();
                    }
                }
            }
            if ($request->old_motors) {
                foreach ($request->old_motors as $old_motor) {
                    if (array_key_exists('model_id', $old_motor) && $old_motor['model_id'] != null) {
                        $old_product_motor                 = ProductMotor::find($old_motor['product_motor_id']);
                        $old_product_motor->product_id     = $product->id;
                        $old_product_motor->brand_model_id = $old_motor['model_id'];
                        $old_product_motor->save();
                    }
                }
            }

        });

        return $this->success(new VendoProductResource($product));
    }

    public function productDelete($id) {
        $product = Product::findOrFail($id);

        (new ImageUoloadService())->deleteFile($product->video);
        (new ImageUoloadService())->deleteFile($product->catelogue_pdf);

        foreach (addUrl(collect(json_decode($product->images))) as $photo) {
            (new ImageUoloadService())->deleteImage($photo);
        }

        foreach ($product->catelogues as $catelogue) {
            (new ImageUoloadService())->deleteImage($catelogue->image);
        }

        $product->delete();

        return $this->success('Product has been deletd');

    }

    private function catelogueStore($catelogues, $product_id) {
        foreach ($catelogues as $catelogue) {
            if (array_key_exists('image', $catelogue) && $catelogue['title'] !== null) {
                $product_catelogue             = new ProductCatelogue();
                $product_catelogue->product_id = $product_id;
                $product_catelogue->title      = $catelogue['title'];
                $product_catelogue->sku        = $catelogue['sku'];
                $product_catelogue->image      = (new ImageUoloadService())->storeImage($catelogue['image'], 'product/catelogues/', 374, 260);
                $product_catelogue->save();
            }
        }
    }

    private function saveCommonData($product, $request) {
        $product->name            = $request->name;
        $product->slug            = Str::slug($request->name);
        $product->shop_id         = $this->shop_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->brand_id        = $request->brand_id;
        $product->brand_model_id  = $request->brand_model_id;
        $product->sku             = $request->sku ?? Str::slug($request->name);

        $product->colors            = $request->colors ? $this->array_null_remove($request->colors) : null;
        $product->sizes             = $request->sizes ? $this->array_null_remove($request->sizes) : null;
        $product->is_available      = $request->is_available;
        $product->is_visible        = $request->is_visible;
        $product->description       = $request->description;
        $product->regular_price     = $request->regular_price;
        $product->selling_price     = $request->discounted_price ?? $request->regular_price;
        $product->quantity          = $request->quantity;
        $product->discount_type     = $request->discount_type;
        $product->discount          = $request->discount;
        $product->discounted_price  = $request->discounted_price;
        $product->additional_name_1 = $request->additional_name_1;
        $product->additional_name_2 = $request->additional_name_2;
        $product->additional_name_3 = $request->additional_name_3;
        $product->additional_name_4 = $request->additional_name_4;
        $product->additional_name_5 = $request->additional_name_5;

        return $product;
    }

    public function productReviewApprove($id){
        $review = ProductReview::findOrFail($id);
        $review->status = !$review->status;
        $review->save();

        $review->load('user');

        $product = Product::findOrFail($review->product_id);
        $average = getAverageRating($product->reviews->count(), $product->reviews->sum('rating'));
        $product->average_rating = $average;
        $product->save();

        return $this->success(new ReviewResource($review));
    }
}
