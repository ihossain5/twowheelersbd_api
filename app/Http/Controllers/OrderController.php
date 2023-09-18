<?php

namespace App\Http\Controllers;

use App\Constants\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $vendor_id, $shop_id;

    public function __construct() {
        $this->shop_id = auth('vendor')->user()?->shop?->id;
        $this->vendor_id = auth('vendor')->user()?->id;
    }

    public function totalOrders(){
        $data['total-order'] = Order::query()->select('id')->where('shop_id',$this->shop_id)->count();
        $data['processing-order'] = $this->statusWiseOrderCount(OrderStatus::PROCESSING);

        $data['shipped-order'] = $this->statusWiseOrderCount(OrderStatus::SHIPPED);
        $data['deliverd-order'] = $this->statusWiseOrderCount(OrderStatus::DELIVERED);
        $data['cancelled-order'] = $this->statusWiseOrderCount(OrderStatus::CANCELLED);
        $data['refund-processing-order'] = $this->statusWiseOrderCount(OrderStatus::REFUND_PROCESSING);
        $data['refund-rejected-order'] = $this->statusWiseOrderCount(OrderStatus::REFUND_REJECTED);
        $data['refunded-order'] = $this->statusWiseOrderCount(OrderStatus::REFUNDED);

       return $this->success($data);
    }

    public function statusWiseOrderCount($status){
      return Order::query()->select('id')
        ->where('shop_id',$this->shop_id)
        ->where('status',$status)
        ->count();
    }

    public function allOrders(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $orders = Order::query()
        ->where('shop_id',$this->shop_id);

        if($orders->count() < 1){
            return $this->errorResponse(null,'Shop');
        }

        $orders =  $orders->paginate($this->pagination);

        return $this->success(OrderResource::collection($orders)->response()->getData(true));
    }

    public function pendingOrders(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $orders = Order::query()
        ->where('shop_id',$this->shop_id)
        ->where('status',OrderStatus::PROCESSING);

        if($orders->count() < 1){
            return $this->errorResponse(null,'Shop');
        }

        $orders =  $orders->paginate($this->pagination);

        return $this->success(OrderResource::collection($orders)->response()->getData(true));
    }
}
