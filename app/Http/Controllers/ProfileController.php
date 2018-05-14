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
     * @api {POST} /profile Update profile
     * @apiName Update profile
     * @apiGroup Profile
     * @apiDescription Create or Update your profile.
     *
     * @apiParam {String} [firstname] The first name of the user.
     * @apiParam {String} [lastname] The lastname of the user.
     * @apiParam {String} [nickname] The nickname of the user.
     * @apiParam {String} [gender] male or female.
     * @apiParam {String} [gender] male or female.
     * @apiParam {Integer} [age] 18 <= age <= 99.
     * @apiParam {String} [The] bio.
     *
     * @apiHeader (Headers) {String} Authorization The JWT authorization value.
     *
     * @apiSuccessExample {json} Success-Response:
     * {
     *      "data": {
     *          "id": 1,
     *          "user_id": 1,
     *          "firstname": "John",
     *          "lastname": "Doe",
     *          "nickname": null,
     *          "gender": null,
     *          "age": null,
     *          "bio": null,
     *          "profile_picture": null,
     *          "cover_picture": null,
     *          "created_at": "2018-05-14 07:22:50",
     *          "updated_at": "2018-05-14 08:07:48"
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *         "error": {
     *             "gender": [
     *                 "The selected gender is invalid."
     *             ]
     *         },
     *         "status": 400
     *     }
     *
     * @apiError 4xx The message indicates the error.
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

        $user = JWTAuth::parseToken()->authenticate();

        if ($user->profile()->exists()) {
            $user->profile()->update($input);
        } else {
            $user->profile()->create($input);
        }
        return $this->respond($user->profile);
    }

    /**
     * @api {GET} /profile Get profile
     * @apiName Get profile
     * @apiGroup Profile
     * @apiDescription Returns your profile.
     * @apiHeader (Headers) {String} Authorization The JWT authorization value.
     *
     * @apiSuccessExample {json} Success-Response:
     * {
     *      "data": {
     *          "id": 1,
     *          "user_id": 1,
     *          "firstname": "John",
     *          "lastname": "Doe",
     *          "nickname": null,
     *          "gender": null,
     *          "age": null,
     *          "bio": null,
     *          "profile_picture": null,
     *          "cover_picture": null,
     *          "created_at": "2018-05-14 07:22:50",
     *          "updated_at": "2018-05-14 08:07:48"
     *      }
     * }
     *
     * @apiError 4xx The message indicates the error.
     */
    public function show()
    {
        return  $this->respond(
            JWTAuth::parseToken()
                ->authenticate()
                ->profile
        );
    }
}
