<?php

namespace App\Services;

use App\Constants\OrderStatus;
use App\Events\PushNotification;
use App\Exceptions\RefundNotEligibleException;
use App\Http\Controllers\Utility\Utils;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;

class OrderService {
    public function changeStatus($order, $status, $cause = '') {
        // dd($status);
        $message = 'Your order #' . $order->order_id . ' status has been changed from ' . $order->status . ' to ' . $status;

        $order->status = $status;

        if ($status == OrderStatus::CANCELLED) {
            $this->adjustQuantity($order->id);

            $order->canceled_at = Carbon::now();

            if ($cause !== '') {
                $order->cancelation_cause = $cause;
            }

        } else if ($status == OrderStatus::REFUND_PROCESSING) {
            if ($order->status !== OrderStatus::DELIVERED) {
                throw new RefundNotEligibleException('Order Must Be Delivered');
            } else if (Carbon::now() > Carbon::parse($order->delivered_at)->addDays(3)) {
                throw new RefundNotEligibleException('Refund time expired');

            } else {
                $order->refund_cause = $cause;
            }

        } else if ($status == OrderStatus::SHIPPED) {
            $order->shipped_at = Carbon::now();
        } else if ($status == OrderStatus::DELIVERED) {
            $order->delivered_at = Carbon::now();

        } else if ($status == OrderStatus::REFUNDED) {
            $order->refunded_at = Carbon::now();
            $order->is_refunded = 1;
        } else if ($status == OrderStatus::REFUND_REJECTED) {
            $order->refunded_at = null;
            $order->is_refunded = 0;
        }

        $order->save();

        $to    = $order->user->device_id ?? '';
        $title = 'Order Status Has Changed';

        $sms = Utils::sendSms($order->user->address()->first()->mobile, $message);
        event(new PushNotification($to, $title, $message));
        event(new PushNotification($order?->shop?->owner?->device_id ?? '', $title, $message));
        // (new PushNotification())->sendToOne($to, $title, $message);
        // (new PushNotification())->sendToOne($order?->shop?->owner?->device_id ?? '', $title, $message);

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
