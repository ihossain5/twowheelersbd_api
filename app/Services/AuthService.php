<?php

namespace App\Services;

use App\Exceptions\InvalidAuthenticateException;
use App\Exceptions\UserNotVerifyException;
use App\Http\Controllers\Utility\Utils;
use App\Models\ShopOwner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthService
{

    public function login($request_data, $type = null)
    {
        $credentials = array('mobile'=>$request_data['mobile'], 'password'=> $request_data['password']);

        if (!$token = auth($type)->attempt($credentials)) {
            throw new InvalidAuthenticateException('Unathorized');
        }

        $user = auth($type)->user();
        $user->device_id = $request_data['device_id'];
        $user->save();

        if( $type == null && $user->is_verified == 0) throw new UserNotVerifyException('Account Not Verify');

        return $this->respondWithToken($token, $type);
    }

    public function register($request_data, $type = null)
    {
        $exprireTime = Carbon::now()->addMinute(5);
        $otp = rand(1000, 9999);

        if ($type != null) {
            $data = new ShopOwner();
            $data->name = $request_data['name'];
            $data->slug = Str::slug($request_data['name']);
            $data->mobile = $request_data['mobile'];
            $data->password = $request_data['password'];
            $data->email = $request_data['email'];
            $data->status = 'Pending';
            $data->otp = $otp;
            $data->otp_expires_time = $exprireTime;
            $data->save();

        } else {
            $data = new User();
            $data->name = $request_data['name'];
            $data->mobile = $request_data['mobile'];
            $data->email = $request_data['email'];
            $data->password = $request_data['password'];
            $data->otp_code = $otp;
            $data->otp_expire_time = $exprireTime;
            $data->save();

            $sms = Utils::sendSms($request_data['mobile'],'Your otp code is '. $otp);
        }

        $token = Auth::guard($type)->login($data);

        return $this->respondWithToken($token,$type);
    }

    public function forgotPassword($mobile, $type = null){
        $exprireTime = Carbon::now()->addMinute(5);
        $otp = generateOtp();

        if ($type != null) {
            $data =  ShopOwner::query()->where('mobile',$mobile)->first();
            $data->otp = $otp;
            $data->otp_expires_time = $exprireTime;
        }else{
            $data = User::query()->where('mobile',$mobile)->first();
            $data->otp_code = $otp;
            $data->otp_expire_time = $exprireTime;
        }
        $data->save();

        Utils::sendSms($mobile,'Your otp code is '. $otp);

        return $data;
    }

    protected function respondWithToken($token, $type)
    {

        $user = auth($type)->user();
        $user->token = $token;
        $user->token_type = 'bearer';

        return $user;
    }

}
