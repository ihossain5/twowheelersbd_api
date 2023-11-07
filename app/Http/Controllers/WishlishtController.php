<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistRequest;
use App\Http\Resources\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlishtController extends Controller
{
    protected $user_id;

    public function __construct() {
        $this->user_id = auth()->user()?->id;
    }

    public function wishlists(Request $request){
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $wishlists = $this->userWishlistModel()->with('product');

        $wishlists = $wishlists->latest()->paginate($this->pagination);

        return $this->success(WishlistResource::collection($wishlists)->response()->getData(true));
    }

    public function wishlistAdd(WishlistRequest $request){
        $product = Product::findOrFail($request->product_id);
        $wishlist = new Wishlist();
        $wishlist->product_id = $request->product_id; 
        $wishlist->user_id = $this->user_id; 
        $wishlist->save();

        return $this->success(new WishlistResource($wishlist));
    }  
    
    public function wishlistRemove(WishlistRequest $request){
        $wishlist =  $this->userWishlistModel()->where('product_id',$request->product_id)->first();

        if(!$wishlist){
            return $this->errorResponse($request->product_id,'Product');
        }

        $wishlist->delete();

         return $this->success('Successfully removed');
    }

    private function userWishlistModel(){
        return Wishlist::query()->where('user_id',$this->user_id);
    }
}
