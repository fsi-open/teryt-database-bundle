<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use FSi\Bundle\TerytDatabaseBundle\Entity\CommunityType;

class LoadCommunityTypeData implements ORMFixtureInterface, FixtureGroupInterface
{
    /**
     * @var array<int, string>
     */
    protected $communityTypes = [
        1 => 'gmina miejska',
        2 => 'gmina wiejska',
        3 => 'gmina miejsko-wiejska',
        4 => 'miasto w gminie miejsko-wiejskiej',
        5 => 'obszar wiejski w gminie miejsko-wiejskiej',
        8 => 'dzielnica w m.st. Warszawa',
        9 => 'delegatura gminy miejskiej'
    ];

    public static function getGroups(): array
    {
        return ['teryt'];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->communityTypes as $type => $name) {
            $communityTypeEntity = new CommunityType($type, $name);

            $manager->persist($communityTypeEntity);
            $manager->flush();
        }
    }
}
