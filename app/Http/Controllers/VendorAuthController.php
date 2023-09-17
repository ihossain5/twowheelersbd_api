<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidOtpException;
use App\Http\Resources\UserResource;
use App\Models\ShopOwner;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorAuthController extends Controller
{
    public $auth;

    public function __construct(AuthService $auth) {
        $this->auth = $auth;
    }

    public function login(Request $request)
    {
        return $this->success(new UserResource($this->auth->login($request->all(),'vendor')));
    }   
    
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:255|unique:shop_owners',
            'password' => 'required|string|min:6',
        ]);

       $data =   $this->auth->register($request->all(),'vendor');

        return $this->success('Successfully registered! Please verify your account by providing otp. Vendor Id is '.$data->id);
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

        $token = Auth::guard('vendor')->login($vendor);

        $vendor->token = $token;
        $vendor->token_type = 'bearer';

        return $this->success(new UserResource($vendor));
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        return $this->success(new UserResource(auth('vendor')->user()));
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
}
