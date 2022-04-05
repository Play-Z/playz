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
            'tournament[game]'=>'LoL',
            'tournament[startAt]'=> new \DateTime() ,
            'tournament[startInscriptionAt]' => new \DateTime('2022-01-01 15:00:00')
        ]) ;

        $crawl = $client->request('GET','/play/tournament/') ;


    }

}