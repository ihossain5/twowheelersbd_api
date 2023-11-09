<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function applyCoupon(Request $request){

        $this->validate($request,['coupon_code' => 'required','sub_total'=> 'required']);

        $coupon = Coupon::where('coupon_code',$request->coupon_code)->where('status',1)->first();

        if($coupon){
            $user_coupon_exists = UserCoupon::query()
            ->where('user_id',auth()->user()->id)
            ->where('coupon_id',$coupon->id)
            ->first();

            if($user_coupon_exists) return $this->error('You have applied this coupon already', 'Already Used');

            $sub_total = floatval(preg_replace('/[^\d.]/', '', $request->sub_total));

            if($sub_total < $coupon->minimum_purchase_amount){
                return $this->error('You must have to purchase more than BDT '.$coupon->minimum_purchase_amount, 'Minimum Purchase Amount Not Fulfilled');
            }

            if($coupon->coupon_type == 'FIXED'){
                $discount = $coupon->amount;
            }else{
                $discount = ($sub_total * $coupon->amount) /100;
            }

            $user_coupon = new UserCoupon();
            $user_coupon->user_id = auth()->user()->id;
            $user_coupon->coupon_id = $coupon->id;
            $user_coupon->save();

            $data['discount'] = $discount; 
            $data['coupon_code'] = $coupon->coupon_code; 
            $data['coupon_id'] = $coupon->id; 

            return $this->success($data);

        }else{
            return $this->error('Invalid coupon provided','Not Found');
        }
    }
}
