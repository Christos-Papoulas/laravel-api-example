<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRegisterTest extends TestCase
{
    protected $user;
    protected $password;

    protected function setUp()
    {
        parent::setUp();
        $this->makeUser();
    }

    /** @test **/
    public function aUserCanRegister()
    {
        $this->assertDatabaseMissing(
            'users',
            ['email' => $this->user->email]
        );

        $response = $this->post(
            '/api/register',
            ['email' => $this->user->email, 'password' => $this->password]
        );
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertDatabaseHas(
            'users',
            ['email' => $this->user->email]
        );
    }
}
