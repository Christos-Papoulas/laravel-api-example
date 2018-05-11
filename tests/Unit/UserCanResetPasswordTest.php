<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserCanResetPasswordTest extends TestCase
{
    /** @test **/
    public function aUserCanResetThePassword()
    {
        $this->createUser();

        $response = $this->post(
            '/api/password/reset',
            ['email' => $this->user->email]
        );
        $response->assertStatus(200);
    }

    /** @test **/
    public function aUserCanNotResetAPasswordThatIsNotEmail()
    {
        $response = $this->post(
            '/api/password/reset',
            ['email' => 'notanemail']
        )->assertStatus(400);
    }
}
