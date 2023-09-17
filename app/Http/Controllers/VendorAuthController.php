<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

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
