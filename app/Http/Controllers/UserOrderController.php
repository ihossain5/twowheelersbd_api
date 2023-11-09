<?php

namespace App\Http\Controllers;

use App\Constants\OrderStatus;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Resources\UserOrderDetailResource;
use App\Http\Resources\UserOrderResource;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\OrderService;
use Illuminate\Http\Request;

class UserOrderController extends Controller {
    protected $user_id;

    public function __construct() {
        $this->user_id = auth()->user()?->id;
    }

    public function orders(Request $request) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $orders = $this->order()
            ->select('order_id', 'status', 'created_at')
            ->when(!empty($request->status), function ($query) use ($request) {
                $query->where('status', $request->status);
            });

        if ($orders->count() < 1) {
            return $this->errorResponse($this->user_id, 'User');
        }

        $orders = $orders->latest()->paginate($this->pagination);

        return $this->success(UserOrderResource::collection($orders)->response()->getData(true));
    }

    public function orderTrack($order_id) {
        $order = $this->order()->where('order_id', $order_id)->firstOrFail();

        $address = UserAddress::query()->select('name','email','mobile','address')->where('user_id', $this->user_id)->first();

        $order->info = json_decode($address);

        return $this->success(new UserOrderResource($order));
    }

    public function orderDetails($order_id) {
        $order = $this->order()->with('shop:id,name','items','items.product:id,name,images,brand_id','items.product.brand:id,name' ,'user:id,name','user:address')->where('order_id', $order_id)->firstOrFail();

        $address = UserAddress::query()->select('name','email','mobile','address')->where('user_id', $this->user_id)->first();

        $order->info = json_decode($address);

        return $this->success(new UserOrderDetailResource($order));
    }

    public function orderCancel($order_id, Request $request){
       $this->validate($request,['cancelation_cause'=> 'required']);

       $order =  $this->order()->where('order_id', $order_id)->firstOrFail();
      
       (new OrderService())->changeStatus($order, OrderStatus::CANCELLED ,$request->cancelation_cause) ;

       return $this->success(new UserOrderDetailResource($order));
    }   
    
    public function refundRequestOrder($order_id, Request $request){
       $this->validate($request,['refund_cause'=> 'required']);

       $order =  $this->order()->where('order_id', $order_id)->firstOrFail();

       (new OrderService())->changeStatus($order, OrderStatus::REFUND_PROCESSING ,$request->refund_cause) ;

       return $this->success(new UserOrderDetailResource($order));
    }

    private function order() {
        return Order::query()->where('user_id', $this->user_id);
    }

    public function orderCreate(OrderStoreRequest $request){

    }
}
