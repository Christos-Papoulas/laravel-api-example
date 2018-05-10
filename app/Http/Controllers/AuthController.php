<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\User;
use Validator;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * @api {POST} /login Login
     * @apiName Login
     * @apiGroup Authentication
     *
     * @apiParam {String} email.
     * @apiParam {String} password.
     *
     * @apiSuccess {String} The JWT token.
     * @apiError 4xx The message indicates the error.
     */
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

    /**
     * @api {POST} /register Register
     * @apiName Register a new Account
     * @apiGroup Authentication
     *
     * @apiParam {String} email.
     * @apiParam {String} password.
     *
     * @apiSuccess {String} The JWT token.
     * @apiError 4xx The message indicates the error.
     */
    public function register(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $rules  = [
            'email' =>
                'required|email|max:255|unique:users,email',
            'password' => 'required|min:6',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                ->respondError($validator->messages());
        }

        User::insert([
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password'])
        ]);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->setStatusCode(Response::HTTP_UNAUTHORIZED)
                    ->respondError('Invalid credentials');
            }
        } catch (JWTException $e) {
            return $this->respondServerError('Could not create token.');
        }

        return $this->respond(['token' => "Bearer $token"]);
    }
}
