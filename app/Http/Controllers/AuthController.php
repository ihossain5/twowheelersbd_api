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

        return $this->success('Otp has sent to given number. User Id is '.$data->id);
    }

    public function forgetPasswordOtpVerify(Request $request){
        $user = $this->verifyOtp($request); 

        return $this->success('Successfully otp verified! user ID: '.$user->id);
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


}
