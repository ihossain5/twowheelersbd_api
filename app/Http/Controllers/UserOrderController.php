<?php

namespace App\Http\Controllers;

use App\Constants\OrderStatus;
use App\Http\Controllers\Utility\Utils;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Resources\UserOrderDetailResource;
use App\Http\Resources\UserOrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\UserAddress;
use App\Services\OrderService;
use App\Services\PushNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // dd($request->all());

        $orderId = Utils::getOrderId();

        DB::transaction(function() use($request, $orderId){
            $this->createOrUpdateAddress($request);
            
            foreach ($request->shops as $shop_id => $shop_arry) {
                $sub_total = array_sum(array_column($shop_arry, 'total_price'));
    
                $shop = Shop::find($shop_id);
    
                if($shop->division == $request->division){
                    $estimate_time = Carbon::now()->addDays(3);
                }else{
                    $estimate_time = Carbon::now()->addDays(5);
                }

                $total = $request->discount ?  $sub_total + $shop->delivery_charge - $request->discount : $sub_total + $shop->delivery_charge;
    
                $order = new Order();
                $order->user_id = auth()->user()->id;
                $order->shop_id = $shop_id;
                $order->order_id = $orderId;
                $order->note = $request->order_note ?? null;
                $order->sub_total = $sub_total;
                $order->delivery_charge = $shop->delivery_charge;
                $order->discount = $request->discount ?? 0;
                $order->total = $total;
                $order->payment_method = $request->payment_method ?? 'Cash';
                $order->payment_status = $request->payment_status ?? 0;
                $order->estimate_delivery_time = $estimate_time;
                $order->two_wheel_commission = Utils::calculateCommission($sub_total, $shop->commission_rate);
                $order->save();
    
                foreach($shop_arry as $data){
                    // dd($shop_arry);
                    $order_item = new OrderItem();
                    $order_item->order_id = $order->id;
                    $order_item->product_id = $data['product_id'];
                    $order_item->price = $data['price'];
                    $order_item->quantity = $data['quantity'];
                    $order_item->total_price = $data['total_price'];
                    $order_item->size = $data['size'];
                    $order_item->color = $data['color'];
                    $order_item->save();

                    $product = Product::findOrFail($data['product_id']);
                    $product->quantity = $product->quantity - $data['quantity'];

                    if($product->quantity < 1){
                        $product->is_available = 0;
                    }
                    $product->save();
                }

                $message = 'Thanks for purchasing from Two Wheelers Bd. Your order ID is #'. $order->order_id;
                $to = auth()->user()->device_id ?? '';
                $title = 'Order has placed';

                $sms = Utils::sendSms($request->mobile, $message);
               (new PushNotification())->sendToOne($to, $title, $message);

                //send notification to vendor
               (new PushNotification())->sendToOne($shop->owner->device_id, $title, 'You have received an order. Order Id is #'. $order->order_id);

            }
        });

        $arr['message'] = 'Success! Order has been placed';
        $arr['order_id'] = $orderId;

        return $this->success($arr);
    }

    private function createOrUpdateAddress($request){
        $address = UserAddress::query()->where('user_id', auth()->user()->id)->first();
        if(!$address){
            $user_address = new UserAddress();
            $user_address->user_id = auth()->user()->id;
            $user_address->name = $request->name;
            $user_address->mobile = $request->mobile;
            $user_address->email = $request->email;
            $user_address->division = $request->division;
            $user_address->district = $request->district;
            $user_address->address = $request->address;
            $user_address->save();
        }else{
            $address->name = $request->name;
            $address->mobile = $request->mobile;
            $address->email = $request->email;
            $address->division = $request->division;
            $address->district = $request->district;
            $address->address = $request->address;
            $address->save();
        }

        return $address;
    }
}
