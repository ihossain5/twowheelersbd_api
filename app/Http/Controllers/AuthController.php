<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidOtpException;
use App\Exceptions\UserNotVerifyException;
use App\Http\Requests\UserAddressRequest;
use App\Http\Resources\UserAddressResource;
use App\Http\Resources\UserBikeInfoResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserBikeInfo;
use App\Services\AuthService;
use App\Services\ImageUoloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public $auth;

    public function __construct(AuthService $auth) {
        $this->auth = $auth;
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'password' => 'required',
            'device_id' => 'required',
        ]);
        return $this->success(new UserResource($this->auth->login($request->all())));
    }

    public function register(Request $request)
    {
        $exists = User::query()->where('mobile',$request->mobile)->first();

        if($exists && $exists->is_verified == 0){
            throw new UserNotVerifyException('Account Not Verify');
        }else{
            $request->validate([
                'name' => 'required|string|max:255',
                'mobile' => 'required|string|max:255|unique:users',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'photo' => 'image|mimes:jpg,png,jpeg|max:1024',
            ]);
    
            $user = $this->auth->register($request->all());

            $data['message'] = 'Successfully registered! Please verify your account by providing otp';
            $data['user_id'] = $user->id;
    
            return $this->success($data);
        }
    }

    
    public function registerOtpVerify(Request $request){
        $user = $this->verifyOtp($request); 

        $user->is_verified = 1;
        $user->save();

        $token = Auth::login($user);

        $user->token = $token;
        $user->token_type = 'bearer';

        return $this->success(new UserResource($user));
    }

    public function forgetPassword(Request $request){
        $request->validate([
            'mobile' => 'required|exists:users,mobile',
        ]);

        $data = $this->auth->forgotPassword($request->mobile);

        $arry['message'] = 'Otp has sent to given number';
        $arry['user_id'] = $data->id;

        return $this->success($arry);
    }

    public function forgetPasswordOtpVerify(Request $request){
        $user = $this->verifyOtp($request); 

        $arry['message'] = 'Successfully otp verified';
        $arry['user_id'] = $user->id;

        return $this->success($arry);
    }

    public function recoverPassword(Request $request){
        $request->validate([
            'id' => 'required',
            'device_id' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::findOrFail($request->id);

        $user->password = $request->password;
        $user->device_id = $request->device_id;
        $user->save();

        $token = Auth::login($user);
        $user->token = $token;

        return $this->success(new UserResource($user));
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        return $this->success(new UserResource(auth()->user()));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function verifyOtp($request){
        $request->validate([
            'id' => 'required',
            'otp' => 'required|string|max:4',
        ]);
        
        $user = User::query()->where('id',$request->id)->where('otp_code',$request->otp)->first();
        if(!$user){
            throw new InvalidOtpException('Otp mismatch');
        }

        return $user;
    }

    public function getBikeInfo()
    {
        $bike_info = UserBikeInfo::query()->where('user_id',auth()->user()->id)->first();

        return $this->success(new UserBikeInfoResource($bike_info));
    }
    
    public function storeBikeInfo(Request $request){
        $this->validate($request,['brand_id' => 'required', 'model_id' => 'required', 'name' => 'required' ]);

        $bike_info = UserBikeInfo::query()->where('user_id',auth()->user()->id)->first();

        if(!$bike_info){
            $bike_info = new UserBikeInfo();
        }
    
        $bike_info->user_id = auth()->user()->id;
        $bike_info->brand_id = $request->brand_id;
        $bike_info->model_id = $request->model_id;
        $bike_info->name = $request->name;
        $bike_info->save();

        return $this->success(new UserBikeInfoResource($bike_info));
    }

    public function updateProfie(Request $request){
        $this->validate($request,['name'=> 'required']);

        $user = auth()->user();
        $image = $user->photo;

        if($request->image){
            if($image) (new ImageUoloadService())->deleteImage($image);
            $image = (new ImageUoloadService())->storeImage($request->image, 'user/',300,300);
            $user->photo = $image;
        }

     
        $user->name = $request->name;
        $user->save();

        return $this->success(new UserResource($user));
    }

    public function updateDeliveryAddress(UserAddressRequest $request, $id){
        $address = UserAddress::where('id',$id)->where('user_id',auth()->user()->id)->first();

        if($address){
            $address->update($request->validated());
            return $this->success(new UserAddressResource($address));
        }else{
            return $this->errorResponse($id,'Address');
        }
        
    }
}
