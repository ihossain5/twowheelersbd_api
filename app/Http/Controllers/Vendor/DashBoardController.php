<?php

namespace App\Http\Controllers\Vendor;

use App\Constants\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashBoardController extends Controller {
    public function dashboard(Request $request) {
        if ($request->has('start_date') && $request->has('end_date')) {

            $request->validate([
                'start_date' => 'date_format:Y-m-d',
                'end_date' => 'date_format:Y-m-d',
            ]);
            
            $from_date = $request->start_date;
            $to_date   = $request->end_date;
        } else {
            $from_date = date('Y-m-d');
            $to_date   = date('Y-m-d');
        }

        $data                = [];
        $data['total_order'] = Order::query()
            ->whereBetween('created_at', [$from_date, $to_date . " 23:59:59"])
            ->where('shop_id', auth('vendor')->user()->shop?->id)
            ->count();

        $data['total_sale'] = Order::query()
            ->whereBetween('created_at', [$from_date, $to_date . " 23:59:59"])
            ->where('status', OrderStatus::DELIVERED)
            ->where('shop_id', auth('vendor')->user()->shop?->id)
            ->selectRaw('SUM(total) as total_sale')
            ->first()
            ->total_sale ?? 0;

        $data['total_payable'] = Order::query()
            ->whereBetween('created_at', [$from_date, $to_date . " 23:59:59"])
            ->where('status', OrderStatus::DELIVERED)
            ->where('shop_id', auth('vendor')->user()->shop?->id)
            ->where('is_paid', 0)
            ->where('is_deliver_by_admin', 0)
            ->sum('two_wheel_commission');

        $data['total_receiveable'] = Order::query()
            ->whereBetween('created_at', [$from_date, $to_date . " 23:59:59"])
            ->where('status', OrderStatus::DELIVERED)
            ->where('shop_id', auth('vendor')->user()->shop?->id)
            ->where('is_deliver_by_admin', 1)
            ->where('is_paid_to_owner', 0)
            ->selectRaw('SUM(total - two_wheel_commission) as total_receiveable')
            ->first()
            ->total_receiveable ?? 0;

        $deliver_by_admin_revenue = Order::query()
            ->whereBetween('created_at', [$from_date, $to_date . " 23:59:59"])
            ->where('status', OrderStatus::DELIVERED)
            ->where('shop_id', auth('vendor')->user()->shop?->id)
            ->where('is_deliver_by_admin', 1)
            ->where('is_paid_to_owner', 1)
            ->selectRaw('SUM(total - two_wheel_commission) as deliver_by_admin_revenue')
            ->first()
            ->deliver_by_admin_revenue ?? 0;

        $deliver_by_owner_revenue = Order::query()
            ->whereBetween('created_at', [$from_date, $to_date . " 23:59:59"])
            ->where('status', OrderStatus::DELIVERED)
            ->where('shop_id', auth('vendor')->user()->shop?->id)
            ->where('is_deliver_by_admin', 0)
            ->selectRaw('SUM(total - two_wheel_commission) as deliver_by_owner_revenue')
            ->first()
            ->deliver_by_owner_revenue ?? 0;

        $data['revenue'] = $deliver_by_owner_revenue + $deliver_by_admin_revenue;

        $data['total_pending_order']     = $this->order($from_date, $to_date, OrderStatus::PROCESSING)->count();
        $data['total_delivered_order']   = $this->order($from_date, $to_date, OrderStatus::DELIVERED)->count();
        $data['total_cancelled_order']   = $this->order($from_date, $to_date, OrderStatus::CANCELLED)->count();
        $data['total_shipped_order']     = $this->order($from_date, $to_date, OrderStatus::SHIPPED)->count();
        $data['total_refunded_order']    = $this->order($from_date, $to_date, OrderStatus::REFUNDED)->count();
        $data['total_refund_processing'] = $this->order($from_date, $to_date, OrderStatus::REFUND_PROCESSING)->count();
        $data['total_refund_rejected']   = $this->order($from_date, $to_date, OrderStatus::REFUND_REJECTED)->count();

        return $this->success($data);
    }

    private function order($from_date, $to_date, $status) {
        return Order::query()
            ->select('id')
            ->whereBetween('created_at', [$from_date, $to_date . " 23:59:59"])
            ->where('status', $status)
            ->where('shop_id', auth('vendor')->user()->shop?->id)
            ->get();
    }
}
