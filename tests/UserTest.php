<?php

namespace App\Tests;

use Symfony\Component\Panther\DomCrawler\Crawler;
use Symfony\Component\Panther\PantherTestCase;

class UserTest extends PantherTestCase
{

    public function testLoginUser()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();
        $pantherClient->request('GET', '/login');
        $pantherClient->submitForm('Sign in', ['email' => 'user.test@playz.com', 'password' => 'test']);

        $this->assertSame(self::$baseUri.'/play/dashboard', $pantherClient->getCurrentURL());
        $this->assertSelectorExists('#logout-link');
    }

    /**
     * @depends testLoginUser
     */
    public function testEditProfile()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

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
        $pantherClient->manage()->window()->maximize();
        $pantherClient->request('GET', '/');

        $crawler = $pantherClient->clickLink('Se deconnecter');

        $this->assertSame(self::$baseUri.'/', $pantherClient->getCurrentURL());
        $this->assertSelectorNotExists('#logout-link');
    }

    /**
     * @depends testLogout
     */
    public function testLoginAdmin()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->request('GET', '/login');
        $pantherClient->submitForm('Sign in', ['email' => 'admin.test@playz.com', 'password' => 'test']);

        $this->assertSame(self::$baseUri.'/admin/', $pantherClient->getCurrentURL());
    }

    /**
     * @depends testLoginAdmin
     */
    public function testAdminEditUserError()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Utilisateurs');
        $crawler = $pantherClient->clickLink('Créer un utilisateur');

        $pantherClient->submitForm('Sauvegarder', ['create_user[email]' => 'admin@playz', 'create_user[username]' => 'AdminTest', 'create_user[password][first]' => 'testtest', 'create_user[password][second]' => 'testtest']);

        $crawler = $pantherClient->request('GET', '/admin/user/new');
        $this->assertSame(self::$baseUri.'/admin/user/new', $pantherClient->getCurrentURL());
        //$this->assertSelectorTextContains('.text-red-700', "Cette valeur n'est pas une adresse email valide.");
    }

    /**
     * @depends testLoginAdmin
     */
    public function testAdminLogout()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();
        $pantherClient->request('GET', '/');

        $crawler = $pantherClient->clickLink('Se deconnecter');

        $this->assertSame(self::$baseUri.'/', $pantherClient->getCurrentURL());
        $this->assertSelectorNotExists('#logout-link');
    }

    /**
     * @depends testLogout
     */
    public function testFalseLogUser()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();
        $pantherClient->request('GET', '/login');
        $pantherClient->submitForm('Sign in', ['email' => 'usertestfalse@playz.com', 'password' => 'test']);

        $this->assertSelectorTextContains('.alert-danger', 'Identifiants invalides.');
    }
}
