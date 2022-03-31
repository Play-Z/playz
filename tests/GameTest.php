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
    public function testIndexGames()
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
    public function testCreateGame()
    {
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Jeux');
        $crawler = $pantherClient->clickLink('Créer un jeu');

        $this->assertSame(self::$baseUri.'/admin/game/new', $pantherClient->getCurrentURL());
        $pantherClient->submitForm('Sauvegarder', ['game[name]' => 'JeuTest', 'game[description]' => 'Mon jeu test']);

        $crawler = $pantherClient->request('GET', '/admin/game');
        $this->assertSame('JeuTest', $crawler->filter("#game-JeuTest")->children('.game-name')->text());
        $this->assertSame('Mon jeu test', $crawler->filter("#game-JeuTest")->children('.game-description')->text());
    }

    /**
     * @depends testLoginAdmin
     */
    public function testEditGame(){
        $pantherClient = static::createPantherClient();
        $pantherClient->manage()->window()->maximize();

        $crawler = $pantherClient->clickLink('Jeux');
        $link = $crawler->filter("#game-JeuTest")->children('.game-actions')->children('.edit-button')->link();
        $pantherClient->click($link);

        $this->assertSame(self::$baseUri.'/admin/game/jeutest/edit', $pantherClient->getCurrentURL());
        $pantherClient->submitForm('Sauvegarder', ['game[name]' => 'JeuTestEdit', 'game[description]' => 'Mon jeu test modifier']);

        $crawler = $pantherClient->request('GET', '/admin/game');
        $this->assertSame('JeuTestEdit', $crawler->filter("#game-JeuTestEdit")->children('.game-name')->text());
        $this->assertSame('Mon jeu test modifier', $crawler->filter("#game-JeuTestEdit")->children('.game-description')->text());
    }

    /**
     * @depends testLoginAdmin
     */
    public function testDeleteGame(){
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
//        $pantherClient->wait()->until(WebDriverExpectedCondition::alertIsPresent());
//        $pantherClient->getWebDriver()->switchTo()->alert()->accept();

        //$crawler = $pantherClient->request('GET', '/admin/game');

        $this->assertSelectorNotExists($gameName, 'JeuTestEdit');
        $this->assertSelectorNotExists($gameDescription, 'Mon jeu test modifier');
    }
}
