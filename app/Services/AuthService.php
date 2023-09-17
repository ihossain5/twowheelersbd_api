<?php

namespace App\Services;

use App\Exceptions\InvalidAuthenticateException;

class AuthService {

    public function login($request_data, $type = null)
    {
        $credentials = $request_data;

        if (!$token = auth($type)->attempt($credentials)) {
             throw new InvalidAuthenticateException('Unathorized');
        }

        return $this->respondWithToken($token, $type);
    }

    protected function respondWithToken($token, $type)
    {
       
        $user = auth($type)->user();
        $user->token = $token;
        $user->token_type = 'bearer';

        return $user;
    }

}