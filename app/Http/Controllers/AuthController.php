<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\User;
use Validator;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * @api {POST} /login Login
     * @apiName Login
     * @apiGroup Authentication
     *
     * @apiParam {String} email The email of the user.
     * @apiParam {String} password The password atleast 6 chars.
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
     * @apiParam {String} email The email of the user.
     * @apiParam {String} password The password at least 6 chars.
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

    /**
     * @api {POST} /password/reset Password Reset
     * @apiName Password Reset
     * @apiGroup Authentication
     * @apiDescription An email will be sent to the user with the new password.
     * @apiParam {String} email The email of the user.
     *
     * @apiSuccess {JSON} Success Message
     * @apiError 4xx The message indicates the error.
     */
    public function passwordReset(Request $request)
    {
        $input = $request->only('email');
        $rules  = [
            'email' => 'required|email|max:255'
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                ->respondError($validator->messages());
        }

        $user = User::whereEmail($input['email'])->first();
        /*
         * If the user does not exist return success.
         * Prevent someone from guessing registered users.
         */
        if (! $user) {
            return $this->respond('An email has been sent.');
        }

        $newPassword = str_random(8);
        $hashed_password = bcrypt($newPassword);
        $send = $this->sendNewPassword($user, $newPassword);

        if (!$send) {
            Log::error("Mail not send due to error");
            return $this->respondServerError('Mail not send.');
        }

        $user->update(['password' => $hashed_password]);

        return $this->respond('An email has been sent.');
    }

    /*
     * Send new password with email to the user.
     */
    protected function sendNewPassword($user, $newPassword)
    {
        $title = env('APP_NAME') . " New password";
        $send = Mail::raw(
            "Your new password is ". $newPassword,
            function ($m) use ($user, $title) {
                $m->to($user->email)->subject($title);
                $m->from(
                    'no-reply@'. strtolower(env('APP_NAME')). '.com'
                );
            }
        );

        if (Mail::failures()) {
            return false;
        }
        return true;
    }
}
