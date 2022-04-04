<?php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;

class TeamTest extends PantherTestCase
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
    public function testIndexTeams()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Parcourir les equipes');
        $this->assertSame(self::$baseUri.'/play/team/', $pantherClient->getCurrentURL());
        $this->assertSame('Equipes', $crawler->filter('#team-index-title')->text());
    }
}