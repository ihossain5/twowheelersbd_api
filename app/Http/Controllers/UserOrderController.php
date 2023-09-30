<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserOrderResource;
use App\Models\Order;
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
}
