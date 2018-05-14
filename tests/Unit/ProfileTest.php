<?php

namespace Tests\Unit;

use App\Profile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->createUser();
    }

    /** @test **/
    public function anUnauthenticatedUserCanNotAddHisProfile()
    {
        $aProfile = factory(Profile::class)
            ->raw(['user_id' => $this->user->id]);

        $this->post('api/profile', $aProfile)
            ->assertStatus(401);
        $this->assertDatabaseMissing('profiles', $aProfile);
    }

    /** @test **/
    public function anAuthenticatedUserCanAddHisProfile()
    {
        $aProfile = factory(Profile::class)
            ->raw(['user_id' => $this->user->id]);

        $this->authenticateUser()
            ->post('api/profile', $aProfile)
            ->assertStatus(200);
        $this->assertDatabaseHas('profiles', $aProfile);
    }

    /** @test **/
    public function aUserCanNotAddHisProfileWithInvalidToken()
    {
        $aProfile = factory(Profile::class)
            ->raw(['user_id' => $this->user->id]);

        $this->withHeader('Authorization', 'a.b.c')
            ->post('api/profile', $aProfile)
            ->assertStatus(401)
            ->assertExactJson(array(
                "error" => "Invalid token",
                "status" => 401
            ));

        $this->assertDatabaseMissing('profiles', $aProfile);
    }
}
