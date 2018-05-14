<?php

namespace App\Http\Controllers;

use JWTAuth;
use Validator;
use App\Profile;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class ProfileController extends Controller
{
    use ApiResponse;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->validate([
            'firstname' => 'nullable|string',
            'lastname' => 'nullable|string',
            'nickname' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
            'age' => 'nullable|integer|min:18|max:99',
            'bio' => 'nullable|string',
        ]);

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenBlacklistedException $e) {
            return $this->setStatusCode(Response::HTTP_UNAUTHORIZED)
                ->respondError('Invalid Token.');
        } catch (JWTException $e) {
            return $this->respondServerError('Failed to parse token.');
        }

        if ($user->profile()->exists()) {
            $user->profile()->update($input);
        } else {
            $user->profile()->create($input);
        }
        return $this->respond($user->profile);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show(Profile $profile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profile $profile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
