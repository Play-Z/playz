<?php

namespace App\Tests;

use Symfony\Component\Panther\DomCrawler\Crawler;
use Symfony\Component\Panther\PantherTestCase;

class UserTest extends PantherTestCase
{

    public function testLoginUser()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->request('GET', '/login');
        $pantherClient->submitForm('Sign in', ['email' => 'user.test@playz.com', 'password' => 'test']);
        $crawler = new Crawler();

        $this->assertSame(self::$baseUri.'/play/', $pantherClient->getCurrentURL());
        $this->assertSelectorExists('#logout-link');
    }

    /**
     * @depends testLoginUser
     */
    public function testEditProfile()
    {
        $pantherClient = static::createPantherClient();
        $crawler = new Crawler();

        $crawler = $pantherClient->clickLink('Profil');
        $crawler = $pantherClient->clickLink('Modifier');

        $pantherClient->submitForm('Sauvegarder', ['user_profile[firstname]' => 'Test', 'user_profile[lastname]' => 'Test', 'user_profile[description]' => 'Hello World !']);

        $this->assertSame(self::$baseUri.'/play/user/testuser', $pantherClient->getCurrentURL());
        $this->assertSelectorTextContains('#user-lastname', 'Test');
        $this->assertSelectorTextContains('#user-firstname', 'Test');
        $this->assertSelectorTextContains('#user-description', 'Hello World !');
    }

    /**
     * @depends testLoginUser
     */
    public function testLogout()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->request('GET', '/');
        $crawler = new Crawler();

        $crawler = $pantherClient->clickLink('Se deconnecter');

        $this->assertSame(self::$baseUri.'/', $pantherClient->getCurrentURL());
        $this->assertSelectorNotExists('#logout-link');
    }
}
