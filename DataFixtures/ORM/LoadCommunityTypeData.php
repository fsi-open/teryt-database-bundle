<?php

namespace FSi\Bundle\TerytDatabaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FSi\Bundle\TerytDatabaseBundle\Entity\CommunityType;

class LoadCommunityTypeData implements FixtureInterface
{
    protected $communityTypes = array(
        1 => 'gmina miejska',
        2 => 'gmina wiejska',
        3 => 'gmina miejsko-wiejska',
        4 => 'miasto w gminie miejsko-wiejskiej',
        5 => 'obszar wiejski w gminie miejsko-wiejskiej',
        8 => 'dzielnica w m.st. Warszawa',
        9 => 'delegatura gminy miejskiej'
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->communityTypes as $type => $name)  {
            $communityTypeEntity = new CommunityType();
            $communityTypeEntity->setType($type)
                ->setName($name);

            $manager->persist($communityTypeEntity);
            $manager->flush();
        }
    }
}