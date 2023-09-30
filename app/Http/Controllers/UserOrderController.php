<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserOrderResource;
use App\Models\Order;
use App\Models\UserAddress;
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

        $orders = Order::query()
            ->select('order_id', 'status', 'created_at')
            ->where('user_id', $this->user_id)
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

    private function order() {
        return Order::query()->where('user_id', $this->user_id);
    }
}
