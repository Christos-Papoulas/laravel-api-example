<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserLoginTest extends TestCase
{
    protected $user;
    protected $password;

    protected function setUp()
    {
        parent::setUp();
        $this->password = 'test11';

        $this->user = factory(\App\User::class)
            ->create([
                'password' => bcrypt($this->password)
            ]);
    }

    /** @test **/
    public function aUserCanlogin()
    {
        $response = $this->post(
            '/api/login',
            ['email' => $this->user->email, 'password' => $this->password]
        );

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['token']]);
    }

    /** @test **/
    public function aUserCanNotloginWithInvalidCreds()
    {
        $response = $this->post(
            '/api/login',
            ['email' => $this->user->email, 'password' => 'wrong!']
        );

        $response->assertStatus(401)
            ->assertJsonStructure(['error']);
    }
}
