<?php

namespace Tests\Unit;

use App\User;
use App\Friend;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FriendshipTest extends TestCase
{
    /** @test **/
    public function aUserCanAddAFriend()
    {
        $friend = factory(User::class)->create([
            'password' => bcrypt('123456')
        ]);
        $this->authenticateUser()
            ->post('api/friends/add/'. $friend->id)
            ->assertStatus(200);

        $this->assertDatabaseHas(
            'friend_user',
            array(
                'user_id' => $this->user->id,
                'friend_id' => $friend->id,
                'status' => Friend::PENDING
            )
        );
    }

    /** @test **/
    public function aUserCanAcceptAFriendRequest()
    {
        $friend = factory(User::class)->create();
        $this->createUser();

        $friend->myFriends()->attach(
            $this->user->id,
            ['status' => Friend::PENDING]
        );

        dd($this->authenticateUser()
            ->post('api/friends/add/'. $friend->id));
            // ->assertStatus(200);

        $this->assertDatabaseHas(
            'friend_user',
            array(
                'friend_id' => $this->user->id,
                'user_id' => $friend->id,
                'status' => Friend::ACCEPTED
            )
        );
    }
}
