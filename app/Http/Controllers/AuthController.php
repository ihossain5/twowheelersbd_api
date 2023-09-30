<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidOtpException;
use App\Exceptions\UserNotVerifyException;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
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
    
            return $this->success('Successfully registered! Please verify your account by providing otp. User Id is '.$user->id);
        }
    }

    
    public function verifyOtp(Request $request){
        $request->validate([
            'id' => 'required',
            'otp' => 'required|string|max:4',
        ]);
        
        $user = User::query()->where('id',$request->id)->where('otp_code',$request->otp)->first();
        if(!$user){
            throw new InvalidOtpException('Otp mismatch');
        }

        $user->is_verified = 1;
        $user->save();

        $token = Auth::login($user);

        $user->token = $token;
        $user->token_type = 'bearer';

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


}
