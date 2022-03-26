<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Component\Panther\PantherTestCase;

class UserTest extends PantherTestCase
{

    public function testLogin()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->request('GET', '/login');
        $pantherClient->submitForm('Sign in', ['email' => 'player@playz.com', 'password' => 'test']);

        $this->assertSame(self::$baseUri.'/', $pantherClient->getCurrentURL());
    }

    public function testLogout()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->request('GET', '/login');
        $pantherClient->submitForm('Sign in', ['email' => 'player@playz.com', 'password' => 'test']);

        $this->assertSame(self::$baseUri.'/', $pantherClient->getCurrentURL());
    }

    public function testEditProfile()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->request('GET', '/login');
        $pantherClient->submitForm('Sign in', ['email' => 'player@playz.com', 'password' => 'test']);

        $this->assertSame(self::$baseUri.'/play/user/player/edit', $pantherClient->getCurrentURL());
    }
}
