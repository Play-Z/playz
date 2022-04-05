<?php

namespace App\DataFixtures;

use App\Factory\GameFactory;
use App\Factory\TeamFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class TeamFixtures extends Fixture implements DependentFixtureInterface
{
    private ContainerBagInterface $params;

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {
        $client = new Client();

        $response = $client->request('GET', 'https://api.pandascore.co/teams?sort=&page=1&per_page=64', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->params->get('app.bearer.token'),
                'Accept' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        for ($i = 0; $i < 64; $i++) {

            $players = $data[$i]['players'];
            $users = [];

            if (!empty($players)){

                $playerNumber = count($players);

                for ($j = 1; $j <= $playerNumber-1; $j++) {

                    $player = $players[$j];

                    $user = UserFactory::createOne(
                        [
                            'username' => $player['name'],
                            'firstname' => $player['first_name'],
                            'lastname' => $player['last_name'],
                            'country' => $player['nationality'],
                            'age' => $player['age'],
                            'roles' => ['ROLE_TEAM_MEMBER'],
                            'redditUsername' => strtolower(str_replace(' ', '-', $player['name'])),
                            'twitchUsername' => strtolower(str_replace(' ', '-', $player['name'])),
                            'twitterUsername' => strtolower(str_replace(' ', '-', $player['name'])),
                            'youtubeUsername' => strtolower(str_replace(' ', '-', $player['name'])),
                        ]
                    );

                    array_push($users, $user);

                }

                TeamFactory::createMany(1,
                    [
                        'name' => $data[$i]['name'],
                        'location' => $data[$i]['location'],
                        'created_by' =>
                            UserFactory::new(
                                [
                                    'username' => $players[0]['name'],
                                    'firstname' => $players[0]['first_name'],
                                    'lastname' => $players[0]['last_name'],
                                    'country' => $players[0]['nationality'],
                                    'age' => $players[0]['age'],
                                    'redditUsername' => strtolower(str_replace(' ', '-', $players[0]['name'])),
                                    'twitchUsername' => strtolower(str_replace(' ', '-', $players[0]['name'])),
                                    'twitterUsername' => strtolower(str_replace(' ', '-', $players[0]['name'])),
                                    'youtubeUsername' => strtolower(str_replace(' ', '-', $players[0]['name'])),
                                ]),
                        'game' => GameFactory::find(
                            [
                                'slug' => $data[$i]['current_videogame']['slug']
                            ]
                        ),
                        'users' => $users,
                        'redditUsername' =>  strtolower(str_replace(' ', '-', $data[$i]['name'])),
                        'twitchUsername' => strtolower(str_replace(' ', '-', $data[$i]['name'])),
                        'twitterUsername' => strtolower(str_replace(' ', '-', $data[$i]['name'])),
                        'youtubeUsername' => strtolower(str_replace(' ', '-', $data[$i]['name'])),
                    ]);
                $manager->flush();
            }
        }
    }

    public function getDependencies()
    {
        return [
            GameFixtures::class
        ];
    }
}
