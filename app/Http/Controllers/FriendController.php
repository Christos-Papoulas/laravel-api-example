<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FriendController extends Controller
{
    use ApiResponse;

    /**
     * @api {POST} friends/add/{user} Add a friend
     * @apiName Add a friend
     * @apiGroup Friends
     * @apiDescription Add a friend or accept an existing friend request.
     *
     * @apiParam {String} user The user id.
     *
     * @apiHeader (Headers) {String} Authorization The JWT authorization value.
     *
     * @apiSuccessExample {json} Success-Response:
     *     {
     *         "data": "Request has been sent."
     *     }
     *
     * @apiError 4xx The message indicates the error.
     */
    public function addFriend(User $friend)
    {
        $user = JWTAuth::parseToken()
            ->authenticate();

        $res = $user->sendFriendRequestTo($friend);
        if ($res) {
            return $this->respond(
                'Request has been sent.'
            );
        }
        return $this->setStatusCode(Response::HTTP_NOT_FOUND)
            ->respondError('Friendship problem');
    }
}
