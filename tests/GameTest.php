<?php

namespace App\Tests;

use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\Panther\PantherTestCase;

class GameTest extends PantherTestCase
{

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
    public function testAdminIndexGames()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Jeux');
        $this->assertSame(self::$baseUri.'/admin/game/', $pantherClient->getCurrentURL());
        $this->assertSame('Jeux', $crawler->filter('h1')->text());
    }

    /**
     * @depends testLoginAdmin
     */
    public function testAdminCreateGame()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Jeux');
        $crawler = $pantherClient->clickLink('Créer un jeu');

        $this->assertSame(self::$baseUri.'/admin/game/new', $pantherClient->getCurrentURL());
        $pantherClient->submitForm('Sauvegarder', ['admin_game[name]' => 'JeuTest', 'admin_game[description]' => 'Mon jeu test']);

        $crawler = $pantherClient->request('GET', '/admin/game');
        $this->assertSame('JeuTest', $crawler->filter("#game-JeuTest")->children('.game-name')->text());
        $this->assertSame('Mon jeu test', $crawler->filter("#game-JeuTest")->children('.game-description')->text());
    }

    /**
     * @depends testLoginAdmin
     */
    public function testAdminShowGame(){
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Jeux');
        $link = $crawler->filter("#game-JeuTest")->children('.game-actions')->children('.show-button')->link();
        $pantherClient->click($link);

        $this->assertSame(self::$baseUri.'/admin/game/jeutest', $pantherClient->getCurrentURL());

        $crawler = $pantherClient->request('GET', '/admin/game/jeutest');
        $this->assertSame('JeuTest', $crawler->filter("#game-title")->text());
        $this->assertSame('Mon jeu test', $crawler->filter("#game-description")->text());
    }

    /**
     * @depends testLoginAdmin
     */
    public function testAdminEditGame(){
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Jeux');
        $link = $crawler->filter("#game-JeuTest")->children('.game-actions')->children('.edit-button')->link();
        $pantherClient->click($link);

        $this->assertSame(self::$baseUri.'/admin/game/jeutest/edit', $pantherClient->getCurrentURL());
        $pantherClient->submitForm('Sauvegarder', ['admin_game[name]' => 'JeuTestEdit', 'admin_game[description]' => 'Mon jeu test modifier']);

        $crawler = $pantherClient->request('GET', '/admin/game');
        $this->assertSame('JeuTestEdit', $crawler->filter("#game-JeuTestEdit")->children('.game-name')->text());
        $this->assertSame('Mon jeu test modifier', $crawler->filter("#game-JeuTestEdit")->children('.game-description')->text());
    }

    /**
     * @depends testLoginAdmin
     */
    public function testAdminDeleteGame(){
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Jeux');
        $gameName = $crawler->filter("#game-JeuTestEdit")->children('.game-name')->text();
        $gameDescription = $crawler->filter("#game-JeuTestEdit")->children('.game-description')->text();
        $link = $crawler->filter("#game-JeuTestEdit")->children('.game-actions')->children('.edit-button')->link();
        $pantherClient->click($link);

        $this->assertSame(self::$baseUri.'/admin/game/jeutestedit/edit', $pantherClient->getCurrentURL());
        $crawler = $pantherClient->request('GET', '/admin/game/jeutestedit/edit');
        $form = $crawler->selectButton('Supprimer')->form();

        $pantherClient->click($form);

        $this->assertSelectorNotExists($gameName, 'JeuTestEdit');
        $this->assertSelectorNotExists($gameDescription, 'Mon jeu test modifier');
    }

    public function testLogout()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();
        $pantherClient->request('GET', '/');

        $crawler = $pantherClient->clickLink('Se deconnecter');

        $this->assertSame(self::$baseUri.'/', $pantherClient->getCurrentURL());
        $this->assertSelectorNotExists('#logout-link');
    }
}
