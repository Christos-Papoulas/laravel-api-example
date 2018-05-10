<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserCreateAccountTest extends TestCase
{
    protected $user;
    protected $password;

    /** @test **/
    public function aUserCanCreateAnAccount()
    {
        $this->password = 'Test11';

        $this->user = factory(\App\User::class)
            ->make([
                'password' => bcrypt($this->password)
            ]);

        $response = $this->post(
            '/api/register',
            ['email' => $this->user->email, 'password' => $this->password]
        )->assertStatus(200)
        ->assertJsonStructure(['data' => ['token']]);

        $response = $this->post(
            '/api/login',
            ['email' => $this->user->email, 'password' => $this->password]
        )->assertStatus(200)
        ->assertJsonStructure(['data' => ['token']]);
    }
}
