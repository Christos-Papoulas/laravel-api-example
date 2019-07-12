<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createUser();
    }

    /** @test **/
    public function aUserCanlogin()
    {
        $response = $this->post(
            '/api/login',
            ['email' => $this->user->email, 'password' => $this->password]
        )->assertStatus(200)
        ->assertJsonStructure(['data' => ['token']]);
    }

    /** @test **/
    public function aUserCanNotloginWithInvalidCreds()
    {
        $response = $this->post(
            '/api/login',
            ['email' => $this->user->email, 'password' => 'wrong!']
        )->assertStatus(401)
        ->assertJsonStructure(['error']);
    }
}
