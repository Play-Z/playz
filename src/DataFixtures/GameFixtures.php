<?php

namespace App\DataFixtures;

use App\Factory\GameFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class GameFixtures extends Fixture implements DependentFixtureInterface
{
    private ContainerBagInterface $params;

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {

        $client = new Client();

        $response = $client->request('GET', 'https://api.pandascore.co/videogames?page=number=1&size=20&per_page=20', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->params->get('app.bearer.token'),
                'Accept' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody());

        for ($i = 0; $i < 14; $i++){
            GameFactory::createMany(1,
                [
                    'created_by' => UserFactory::random(),
                    'name' => $data[$i]->name,
                    'slug' => $data[$i]->slug,
                    'path' => "{$data[$i]->slug}.png"
                ]);
        $manager->flush();
        }

    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
