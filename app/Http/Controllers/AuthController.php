<?php

namespace App\Http\Controllers;

use JWTAuth;
use Validator;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponse;

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $rules  = [
            'email' =>
                'required|email|max:255|exists:users,email',
            'password' => 'required|min:6',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                ->respondError($validator->messages());
        }

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->setStatusCode(401)
                    ->respondError('Invalid credentials');
            }
        } catch (JWTException $e) {
            return $this->respondServerError('Could not create token.');
        }

        return $this->respond(['token' => "Bearer $token"]);
    }
}
