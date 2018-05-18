<?php

namespace App\Traits;

use App\User;
use App\Friend;

trait Friendable
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function myFriends()
    {
        // $related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null,
        // $parentKey = null, $relatedKey = null, $relation = null
        return $this->belongsToMany(
            'App\User',
            'friend_user',
            'user_id',
            'friend_id'
        )->withPivot(['status']);
    }

    public function friendsOfMine()
    {
        return $this->belongsToMany(
            'App\User',
            'friend_user',
            'friend_id',
            'user_id'
        )->withPivot(['status']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function mergedFriends($friend = null)
    {
        $friends = collect([]);
        if (is_null($friend)) {
            $friends->push($this->myFriends);
            $friends->push($this->friendsOfMine);
            return $friends->flatten();
        }
        $friends->push($this->myFriends()->where('friend_id', $friend->id)->get());
        $friends->push($this->friendsOfMine()->where('user_id', $friend->id)->get());
        return $friends->flatten();
    }

    /**
     * Alias to eloquent many-to-many relation's attach() method.
     *
     * @param User $user
     * @return bool
     */
    public function sendFriendRequestTo($user)
    {
        if ($user->id == $this->id) {
            abort(400, 'You can not add yourself as a friend');
        }

        $relationships = $this->mergedFriends($user);
        if (count($relationships->all()) == 0) {
            $this->myFriends()->attach($user->id, [
                'status' => Friend::PENDING,
            ]);
            return true;
        } elseif (count($relationships->where('pivot.status', Friend::ACCEPTED)
            ->all()) > 0) {
            abort(400, 'You are already friends');
        }

        $reverse = $relationships->where('pivot.status', Friend::PENDING)
                ->where('pivot.friend_id', $this->id)
                ->first();
        if ($reverse) {
            // Recipient already sent a friend request
            // Accept pending friend request
            $reverse->pivot->status = Friend::ACCEPTED;
            $reverse->pivot->save();
            return true;
        }
        abort(400, 'Something went wrong');
    }
}
