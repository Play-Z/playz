<?php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;

class TournamentTest extends PantherTestCase
{

    public function testConnexionAdmin() {

        $client = static::createPantherClient();
        $client->manage()->window()->maximize() ;

        $client->request('GET', '/login');
        $client->submitForm('Sign in', ['email' => 'admin.test@playz.com', 'password' => 'test']);

        $this->assertSame(self::$baseUri.'/admin/', $client->getCurrentURL()) ;
    }

    /**
     * @depends testConnexionAdmin
     */
    public function testCreateTournament() {
        $client = static::createPantherClient();
        $client->manage()->window()->maximize() ;

        $client->request('GET','/tournament') ;
        $crawler = $client->clickLink('Créer un tournoi') ;

        $this->assertSame(self::$baseUri.'/tournament/new',$client->getCurrentURL());
        $client->submitForm('Sauvegarder',[
            'tournament[name]'=>'ODT Tournament creation',
            'tournament[description]' => 'Tournament created by ODT Test',
            'tournament[max_team_players]'=> 3,
            'tournament[max_team_participant]'=>8,
        ]) ;
        $crawl = $client->request('GET','/tournament/') ;
        $this->assertSame('ODT Tournament creation', $crawl->filter("#odt-tournament-creation")->text());

    }

    /**
     * @depends testCreateTournament
     */
    public function testShowTournament() {
        $client = static::createPantherClient();
        $client->manage()->window()->maximize() ;

        $crawl = $client->request('GET','/play/tournament/') ;
        $this->assertSame('ODT Tournament creation', $crawl->filter("#odt-tournament-creation")->text());

    }

    /**
     * @depends  testCreateTournament
     */
    public function testAnnulationTournament() {
        $client = static::createPantherClient();
        $client->manage()->window()->maximize() ;

        $client->request('GET','/tournament') ;
        $crawler = $client->clickLink('ANNULER') ;
        $this->assertSame(self::$baseUri.'/tournament/',$client->getCurrentURL());
    }

}