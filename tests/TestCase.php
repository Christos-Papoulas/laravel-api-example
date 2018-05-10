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

    public function createUser()
    {
        $this->password = 'test11';

        $this->user = factory(\App\User::class)
            ->create([
                'password' => bcrypt($this->password)
            ]);
    }

    public function makeUser()
    {
        $this->password = 'Test11';

        $this->user = factory(\App\User::class)
            ->make([
                'password' => bcrypt($this->password)
            ]);
    }
}
