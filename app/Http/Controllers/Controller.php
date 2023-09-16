<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public $pagination = 10;

    public function success($result){
        return [
            'status' => 'success',
            'resutls' => $result,
        ];
    }

    public function errorResponse($id, $name){
        return response()->json([
            'status' => false,
            'errors' => 'Not Found',
            'message' => 'No data found with '. $name. ' ID: '.$id,
       ],404);
    }

    
}
