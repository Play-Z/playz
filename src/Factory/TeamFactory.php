<?php

namespace App\Factory;

use App\Entity\Team;
use App\Repository\TeamRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Team>
 *
 * @method static Team|Proxy createOne(array $attributes = [])
 * @method static Team[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Team|Proxy find(object|array|mixed $criteria)
 * @method static Team|Proxy findOrCreate(array $attributes)
 * @method static Team|Proxy first(string $sortedField = 'id')
 * @method static Team|Proxy last(string $sortedField = 'id')
 * @method static Team|Proxy random(array $attributes = [])
 * @method static Team|Proxy randomOrCreate(array $attributes = [])
 * @method static Team[]|Proxy[] all()
 * @method static Team[]|Proxy[] findBy(array $attributes)
 * @method static Team[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Team[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TeamRepository|RepositoryProxy repository()
 * @method Team|Proxy create(array|callable $attributes = [])
 */
final class TeamFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'path' => self::faker()->randomElement(['cloud9_logo.png', 'fnatic_logo.png', 'g2_esports_logo.png', 'karmine_corp_logo.png', 'sk_telecom_t1_logo.png', 'solary_logo.png', 'vitality_logo.png']),
            'emplacement' => 10,
            'public' => self::faker()->boolean(),
            'is_verified' =>self::faker()->boolean(),
            'description' => self::faker()->sentence,
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Team $team): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Team::class;
    }
}
