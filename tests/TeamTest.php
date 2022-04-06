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

    public function testIndexTeams()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Parcourir les equipes');
        $this->assertSame(self::$baseUri.'/play/team/', $pantherClient->getCurrentURL());
        $this->assertSame('Equipes', $crawler->filter('#team-index-title')->text());
    }

    /**
     * @depends testLoginUser
     */
    public function testCreateTeam()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $pantherClient->request('GET', '/play/team');
        $crawler = $pantherClient->request('GET', '/play/team/new');

        $this->assertSame(self::$baseUri.'/play/team/new', $pantherClient->getCurrentURL());
        $pantherClient->submitForm('Sauvegarder', ['create_team[name]' => 'TeamTest']);

        $crawler = $pantherClient->request('GET', '/play/team/teamtest');
        $this->assertSame('TeamTest', $crawler->filter("#team-title")->text());
    }

    /**
     * @depends testCreateTeam
     */
    public function testLogoutUserWithTeam()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();
        $pantherClient->request('GET', '/');

        $crawler = $pantherClient->clickLink('Se deconnecter');

        $this->assertSame(self::$baseUri.'/', $pantherClient->getCurrentURL());
        $this->assertSelectorNotExists('#logout-link');
    }

    /**
     * @depends testLogoutUserWithTeam
     */
    public function testLoginUserWithTeam()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();
        $pantherClient->request('GET', '/login');
        $pantherClient->submitForm('Sign in', ['email' => 'user.test@playz.com', 'password' => 'test']);

        $this->assertSame(self::$baseUri.'/play/dashboard', $pantherClient->getCurrentURL());
        $this->assertSelectorExists('#logout-link');
    }

    /**
     * @depends testLoginUserWithTeam
     */
    public function testShowTeam(){
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Mon équipe');
        $this->assertSame(self::$baseUri.'/play/team/teamtest', $pantherClient->getCurrentURL());

        $crawler = $pantherClient->request('GET', '/play/team/teamtest');
        $this->assertSame('TeamTest', $crawler->filter("#team-title")->text());
    }

    /**
     * @depends testShowTeam
     */
    public function testLeaveTeam(){
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Mon équipe');
        $link = $crawler->filter("#leave-team")->link();
        $pantherClient->click($link);

        $this->assertSame(self::$baseUri.'/play/team/', $pantherClient->getCurrentURL());
        $this->assertSelectorNotExists('#team-TeamTest');
    }
}