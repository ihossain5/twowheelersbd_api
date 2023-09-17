<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;

class Utils extends Controller{

    public static function getBookingPostFix($lenght = 1){
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $code = '';
        for($i = 1; $i <= $lenght; $i++){
            $code = $code.$codeAlphabet[random_int(0, 35)];
        }
        return $code;
    }

    public static function getShopWiseCartItems($cart_items){
        $shops = [];

        foreach ($cart_items as $key => $cart_item) {
            $shops[$cart_item->options->shop_id][$key]['product_id']= $cart_item->id;
            $shops[$cart_item->options->shop_id][$key]['qty']= $cart_item->qty;
            $shops[$cart_item->options->shop_id][$key]['name']= $cart_item->name;
            $shops[$cart_item->options->shop_id][$key]['price']= $cart_item->price;
            $shops[$cart_item->options->shop_id][$key]['color']= $cart_item->options->color;
            $shops[$cart_item->options->shop_id][$key]['size']= $cart_item->options->size;
            $shops[$cart_item->options->shop_id][$key]['shop_id']= $cart_item->options->shop_id;
            $shops[$cart_item->options->shop_id][$key]['delivery_charge']= $cart_item->options->delivery_charge;
            $shops[$cart_item->options->shop_id][$key]['total_price']= $cart_item->price * $cart_item->qty;

        }

        return $shops;
    }

    public static function calculateCommission($total, $commission_rate){
        return ($total * $commission_rate) / 100;
    }
    
    public static function sendSms($number, $message){
        $to = $number;
        $message = $message;

        $url = config('app.sms_gateway_url');


        $data= array(
        'to'=>"$to",
        'message'=>"$message",
        'token'=> config('app.sms_gateway_token')
        ); 

        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresult = curl_exec($ch);

        return $smsresult;
    }
}
