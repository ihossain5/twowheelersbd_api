<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponRequest;
use App\Http\Requests\ShopRequest;
use App\Http\Resources\CouponResource;
use App\Http\Resources\HotDealResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\VideoResource;
use App\Models\Coupon;
use App\Models\HotDeal;
use App\Models\HotDealProduct;
use App\Models\Shop;
use App\Models\ShopVideo;
use App\Services\HotDealService;
use App\Services\ImageUoloadService;
use App\Services\ProductService;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorController extends Controller {

    protected $vendor_id, $shop_id;

    public function __construct() {
        $this->shop_id   = auth('vendor')->user()?->shop?->id;
        $this->vendor_id = auth('vendor')->user()?->id;
    }

    public function shopCreate(ShopRequest $request) {
        if ($this->shop_id == null) {
            $this->validate($request, [
                'name'  => 'required',
                'logo'  => 'required|image|mimes:peg,png,jpg',
                'photo' => 'required|image|mimes:peg,png,jpg',
            ]);
            $logo  = (new ImageUoloadService())->storeImage($request->logo, 'shop/', 180, 218);
            $photo = (new ImageUoloadService())->storeImage($request->photo, 'shop/', 1600, 450);

        } else {
            $this->validate($request, [
                'name'  => 'required',
                'logo'  => 'image|mimes:peg,png,jpg',
                'photo' => 'image|mimes:peg,png,jpg',
            ]);
            if ($request->logo) {

                (new ImageUoloadService())->deleteImage(auth('vendor')->user()->shop->logo);
                $logo = (new ImageUoloadService())->storeImage($request->logo, 'shop/', 180, 218);
            } else {
                $logo = auth('vendor')->user()->shop->logo;
            }

            if ($request->photo) {
                (new ImageUoloadService())->deleteImage(auth('vendor')->user()->shop->photo);
                $photo = (new ImageUoloadService())->storeImage($request->photo, 'shop/', 1600, 450);
            } else {
                $photo = auth('vendor')->user()->shop->photo;
            }
        }

        $shop = Shop::updateOrCreate(
            ['shop_owner_id' => $this->vendor_id],
            [
                'name'            => $request->name,
                'slug'            => Str::slug($request->name),
                'discription'     => $request->discription,
                'division'        => $request->division,
                // 'delivery_charge' => $request->delivery_charge,
                'logo'            => $logo,
                'photo'           => $photo,
            ]
        );
        return $this->success(new ShopResource($shop));
    }

    public function shopDetails() {
        $shop = Shop::query()->where('shop_owner_id', $this->vendor_id)->firstOrFail();

        return $this->success(new ShopResource($shop));
    }

    public function shopVideos(Request $request) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $videos = ShopVideo::query()->where('shop_id', $this->shop_id);

        if ($videos->count() < 1) {
            return $this->errorResponse($this->shop_id, 'Shop');
        }
        $videos = $videos->paginate($this->pagination);

        return $this->success(VideoResource::collection($videos)->response()->getData(true));
    }

    public function shopVideoCreate(Request $request, VideoService $videoService) {
        $request->validate([
            'preview_image' => 'required|image|max:1024|mimes:jpg,jpeg,png',
            'link'          => 'required|string',
            'status'        => 'required|boolean',
        ]);

        $video = $videoService->store($request->all(), $this->shop_id);

        return $this->success(new VideoResource($video));

    }

    public function shopVideoUpdate(Request $request, VideoService $videoService, $id) {
        // dd($request->all());
        $request->validate([
            'preview_image' => 'image|max:1024|mimes:jpg,jpeg,png',
            'link'          => 'required|string',
            'status'        => 'required|boolean',
        ]);

        $video = ShopVideo::findOrFail($id);

        $video = $videoService->store($request->all(), $this->shop_id, $video);

        return $this->success(new VideoResource($video));

    }

    public function shopVideoEdit($id) {
        $video = ShopVideo::findOrFail($id);

        return $this->success(new VideoResource($video));
    }

    public function shopVideoDelete($id) {
        $video = ShopVideo::findOrFail($id);

        (new ImageUoloadService())->deleteImage($video->preview_image);

        $video->delete();

        return $this->success('Video has deleted');
    }

    public function shopDeals(Request $request) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $deals = HotDeal::query()
            ->where('shop_id', $this->shop_id)
            ->when(!empty($request->search), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%');
            });

        if ($deals->count() < 1) {
            return $this->errorResponse(null, 'Shop');
        }
        $deals = $deals->paginate($this->pagination);

        return $this->success(HotDealResource::collection($deals)->response()->getData(true));
    }

    public function dealsProducts($deal_id, ProductService $productService) {
        $ids = HotDealProduct::query()->where('hot_deal_id', $deal_id)->pluck('product_id');

        $products = $productService->select()->whereIn('id', $ids);

        if ($products->count() < 1) {
            return $this->errorResponse($deal_id, 'Hot Deal');
        }

        $products = $products->paginate($this->pagination);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function createDeal(Request $request, HotDealService $hotDealService) {
        // dd($request->all());
        $this->validate($request, [
            'title'                 => 'required',
            'banner'                => 'required|mimes:jpg,jpeg,png',
            'products'              => 'required',
            'products.*.id'         => 'required|unique:hot_deal_products,product_id',
            'products.*.percentage' => 'required',
        ]);
        $hot_deal = $hotDealService->store($request->all(), $this->shop_id);

        return $this->success(new HotDealResource($hot_deal));

    }

    public function editDeal($id) {
        $hot_deal = HotDeal::with('products:id,hot_deal_id,product_id,percentage,discounted_price')
            ->findOrFail($id);
        return $this->success(new HotDealResource($hot_deal));
    }

    public function updateDeal($id, Request $request, HotDealService $hotDealService) {
        // dd($request->all());
        $this->validate($request, [
            'title'        => 'required',
            'banner'       => 'mimes:jpg,jpeg,png',
            'old_products' => 'required',
        ]);
        $deal = HotDeal::findOrFail($id);

        $hot_deal = $hotDealService->store($request->all(), $this->shop_id, $deal);

        return $this->success(new HotDealResource($hot_deal));
    }

    public function deleteDeal($id, HotDealService $hotDealService) {

        $hotDealService->delete($id);

        return $this->success('Item has deleted');
    }

    public function shopCoupons(Request $request){
        $coupons = Coupon::query()->where('shop_id', $this->shop_id);

        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        if ($coupons->count() < 1) {
            return $this->errorResponse($this->shop_id, 'Shop');
        }
        $coupons = $coupons->paginate($this->pagination);

        return $this->success(CouponResource::collection($coupons)->response()->getData(true));
    }

    public function createCoupon(CouponRequest $request){
        $coupon = Coupon::create(array_merge($request->validated(), [
            'shop_id' => $this->shop_id,
        ]));

        return $this->success(new CouponResource($coupon));
    }

    public function editCoupon($id){
        $coupon = Coupon::findOrFail($id);

        return $this->success(new CouponResource($coupon));
    }
    public function updateCoupon($id, CouponRequest $request){
        $coupon = Coupon::findOrFail($id);

        $coupon->update($request->validated());

        return $this->success(new CouponResource($coupon));
    }
    public function deleteCoupon($id){
        $coupon = Coupon::findOrFail($id)->delete();

        return $this->success('coupon has deleted');
    }
}
