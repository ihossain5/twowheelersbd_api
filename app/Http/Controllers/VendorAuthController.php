<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidOtpException;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Resources\VendorResource;
use App\Models\ShopOwner;
use App\Services\AuthService;
use App\Services\ImageUoloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VendorAuthController extends Controller
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

        return $this->success(new VendorResource($this->auth->login($request->all(),'vendor')));
    }   
    
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:255|unique:shop_owners',
            'password' => 'required|string|min:6',
        ]);

        return $this->success(new VendorResource($this->auth->register($request->all(),'vendor')));

        // return $this->success('Successfully registered! Please verify your account by providing otp. Vendor Id is '.$data->id);
    }

    public function forgetPassword(Request $request){
        $request->validate([
            'mobile' => 'required|exists:shop_owners,mobile',
        ]);

        $data = $this->auth->forgotPassword($request->mobile,'vendor');

        return $this->success('Otp has sent to given number. Vendor Id is '.$data->id);
    }

    public function verifyOtp(Request $request){
        $request->validate([
            'id' => 'required',
            'otp' => 'required|string|max:255',
        ]);
        
        $vendor = ShopOwner::query()->where('id',$request->id)->where('otp',$request->otp)->first();
        if(!$vendor){
            throw new InvalidOtpException('Otp mismatch');
        }

        return $this->success('Successfully otp verified! vendor ID: '.$vendor->id);

        // $token = Auth::guard('vendor')->login($vendor);

        // $vendor->token = $token;
        // $vendor->token_type = 'bearer';

        // return $this->success(new VendorResource($vendor));
    }

    public function recoverPassword(Request $request){
        $request->validate([
            'id' => 'required',
            'device_id' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $vendor = ShopOwner::findOrFail($request->id);

        $vendor->password = $request->password;
        $vendor->device_id = $request->device_id;
        $vendor->save();

        $token = Auth::guard('vendor')->login($vendor);
        $vendor->token = $token;

        return $this->success(new VendorResource($vendor));
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        return $this->success(new VendorResource(auth('vendor')->user()));
    }

    public function updateProfile(Request $request)
    {
        $vendor = auth('vendor')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:shop_owners,email, '. $vendor->id,
            'address' => 'required',
            'photo' => 'image|mimes:jpg,jpeg,png|max:1024',
            'mobile' => 'required|string|max:255|unique:shop_owners,mobile, '. $vendor->id,
        ]);


        if($request->photo){
            if($vendor) ( new ImageUoloadService())->deleteImage($vendor->photo);

            $photo = ( new ImageUoloadService())->storeImage($request->photo,'vendor/',50,50);

            $vendor->photo = $photo;
        }

        $vendor->name = $request->name;
        $vendor->slug =Str::slug($request->name);
        $vendor->mobile = $request->mobile;
        $vendor->address = $request->address;
        $vendor->email = $request->email;
        $vendor->save();


        return $this->success(new VendorResource($vendor));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('vendor')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function passwordChange(PasswordChangeRequest $request){
        $vendor = auth('vendor')->user();
        $vendor->password = $request->new_password;
        $vendor->save();

        auth('vendor')->logout();

        return $this->success('Password has been changed. Please login again');

    }
}
