<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    protected $user;
    protected $password;
    protected $token;

    public function createUser()
    {
        $this->password = 'test11';

        $this->user = factory(\App\User::class)
            ->create([
                'password' => bcrypt($this->password)
            ]);
        return $this;
    }

    public function makeUser()
    {
        $this->password = 'Test11';

        $this->user = factory(\App\User::class)
            ->make([
                'password' => bcrypt($this->password)
            ]);
        return $this;
    }

    public function authenticateUser()
    {
        if (! $this->user) {
            $this->createUser();
        }

        if (! $this->token) {
            $this->token = $this->post(
                '/api/login',
                [
                    'email' => $this->user->email,
                    'password' => $this->password,
                ]
            )->json()['data']['token'];
        }

        return $this->withHeader('Authorization', $this->token);
    }
}
