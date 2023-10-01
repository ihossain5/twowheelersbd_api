<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShopResource;
use App\Http\Resources\ShopReviewResourece;
use App\Http\Resources\ShopVideoResourece;
use App\Models\Shop;
use App\Models\ShopReview;
use App\Models\ShopVideo;
use Illuminate\Http\Request;

class ShopController extends Controller {
    public function shopHotdeals() {

    }

    public function singleShop($id) {
        $shop = Shop::query()->findOrFail($id);

        return $this->success(new ShopResource($shop));
    }

    public function shopVideos(Request $request, $id) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $videos = ShopVideo::query()->where('shop_id', $id)->where('status', 1);

        if ($videos->count() < 1) {
            return $this->errorResponse($id, 'Shop');
        }

        $videos = $videos->latest()->paginate($this->pagination);

        return $this->success(ShopVideoResourece::collection($videos)->response()->getData(true));
    }

    public function shopReviews(Request $request, $id) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $reviews = ShopReview::query()->where('shop_id', $id)->where('status', 1);

        if ($reviews->count() < 1) {
            return $this->errorResponse($id, 'Shop');
        }

        $reviews = $reviews->latest()->paginate($this->pagination);

        return $this->success(ShopReviewResourece::collection($reviews)->response()->getData(true));
    }

    public function storeRating($id, Request $request) {
        $this->validate($request,['rating'=> 'required','review'=> 'required']);

        $shop = Shop::findOrFail($id);
        $review          = new ShopReview();
        $review->shop_id = $id;
        $review->user_id = auth()->user()->id;
        $review->rating  = $request->rating;
        $review->review  = $request->review;
        $review->save();

        return $this->success('Review has been added successfully');
    }
}
