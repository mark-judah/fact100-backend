<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Tymon\JWTAuth\Facades\JWTAuth;

class Controller extends BaseController
{
    public function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 60 * 60 * 24 * 7 * 52
        ], 200);
        //60sec*60*24*7=1 week
        //*52=1 year
    }


}
