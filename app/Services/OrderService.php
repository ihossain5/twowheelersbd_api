<?php

namespace App\Services;

use App\Constants\OrderStatus;
use App\Http\Controllers\Utility\Utils;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;

class OrderService {
    public function changeStatus($order, $status, $cause) {
        // dd($status);
        $message = 'Your order #' . $order->order_id . ' status has been changed from ' . $order->status . ' to ' . $status;

        $order->status = $status;

        if ($status == OrderStatus::CANCELLED) {
            $this->adjustQuantity($order->id);

            $order->canceled_at = Carbon::now();
            $order->cancelation_cause = $cause;

        } else if ($status == OrderStatus::REFUND_PROCESSING) {
            $order->refund_cause = $cause;
        }
        $order->save();

        $to    = auth()->user()->device_id ?? '';
        $title = 'Order Status Has Changed';

        $sms = Utils::sendSms($order->user->address()->first()->mobile, $message);
        (new PushNotification())->sendToOne($to, $title, $message);
        (new PushNotification())->sendToOne($order?->shop?->owner?->device_id ?? '', $title, $message);

        return $order;
    }

    public function adjustQuantity($order_id) {
        $order_item = OrderItem::query()->where('order_id', $order_id)->get();

        foreach ($order_item as $key => $item) {
            $product           = Product::findOrFail($item->product_id);
            $product->quantity = $product->quantity + $item->quantity;
            $product->save();
        }

        return true;
    }
}