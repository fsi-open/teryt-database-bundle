<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use FSi\Bundle\TerytDatabaseBundle\Entity\Community;
use FSi\Bundle\TerytDatabaseBundle\Entity\CommunityType;
use FSi\Bundle\TerytDatabaseBundle\Entity\District;
use FSi\Bundle\TerytDatabaseBundle\Entity\Place;
use FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType;
use FSi\Bundle\TerytDatabaseBundle\Entity\Province;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleXMLElement;

// phpcs:disable PSR1.Methods.CamelCapsMethodName
class PlacesNodeConverterSpec extends ObjectBehavior
{
    public function let(ObjectManager $om, ObjectRepository $or): void
    {
        // It is not possible to mock internal classes with final constructor
        $this->beConstructedWith(new SimpleXMLElement('<row></row>'), $om);
        $om->getRepository(Argument::type('string'))->willReturn($or);
        $or->findOneBy(Argument::type('array'))->willReturn();
    }

    public function it_converts_node_to_place_entry(ObjectManager $om, ObjectRepository $or): void
    {
        $xml = <<<EOT
<row>
    <woj>04</woj>
    <pow>11</pow>
    <gmi>05</gmi>
    <rodz_gmi>5</rodz_gmi>
    <rm>01</rm>
    <mz>1</mz>
    <nazwa>Rzeczyca</nazwa>
    <sym>0867650</sym>
    <sympod>0867650</sympod>
    <stan_na>2013-03-06</stan_na>
</row>
EOT;

        $community = new Community(
            new District(new Province(1, 'województwo'), 1, 'Powiat'),
            41105,
            'Gmina',
            new CommunityType(1, 'Gmina wiejska')
        );

        $placeType = new PlaceType(1, 'miasto');

        $or->findOneBy(['code' => 411055])->shouldBeCalled()->willReturn($community);
        $or->findOneBy(['type' => 1])->shouldBeCalled()->willReturn($placeType);

        $place = new Place(867650, 'Rzeczyca', $placeType, $community);

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($place);
    }

    public function it_converts_node_to_place_with_updating_existing_one(
        ObjectManager $om,
        ObjectRepository $or,
        Place $place
    ): void {
        $xml = <<<EOT
<row>
    <woj>04</woj>
    <pow>11</pow>
    <gmi>05</gmi>
    <rodz_gmi>5</rodz_gmi>
    <rm>01</rm>
    <mz>1</mz>
    <nazwa>Rzeczyca</nazwa>
    <sym>0867650</sym>
    <sympod>0867650</sympod>
    <stan_na>2013-03-06</stan_na>
</row>
EOT;

        $community = new Community(
            new District(new Province(1, 'województwo'), 1, 'Powiat'),
            41105,
            'Gmina',
            new CommunityType(1, 'Gmina wiejska')
        );

        $placeType = new PlaceType(1, 'miasto');

        $or->findOneBy(['id' => 867650])->shouldBeCalled()->willReturn($place);
        $or->findOneBy(['code' => 411055])->shouldBeCalled()->willReturn($community);
        $or->findOneBy(['type' => '01'])->shouldBeCalled()->willReturn($placeType);

        $place->setName('Rzeczyca')->shouldBeCalled();
        $place->setType($placeType)->shouldBeCalled();
        $place->setCommunity($community)->shouldBeCalled();
        $place->setParentPlace(null)->shouldBeCalled();

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($place->getWrappedObject());
    }

    public function it_updates_parent_place_in_existing_place(
        ObjectManager $om,
        ObjectRepository $or,
        Place $place,
        Place $parentPlace
    ): void {
        $xml = <<<EOT
<row>
    <woj>04</woj>
    <pow>11</pow>
    <gmi>05</gmi>
    <rodz_gmi>5</rodz_gmi>
    <rm>01</rm>
    <mz>1</mz>
    <nazwa>Rzeczyca</nazwa>
    <sym>0867650</sym>
    <sympod>0867643</sympod>
    <stan_na>2013-03-06</stan_na>
</row>
EOT;

        $community = new Community(
            new District(new Province(1, 'województwo'), 1, 'Powiat'),
            41105,
            'Gmina',
            new CommunityType(1, 'Gmina wiejska')
        );

        $placeType = new PlaceType(1, 'miasto');

        $or->findOneBy(['id' => 867650])->shouldBeCalled()->willReturn($place);
        $or->findOneBy(['id' => 867643])->shouldBeCalled()->willReturn($parentPlace);
        $or->findOneBy(['code' => 411055])->shouldBeCalled()->willReturn($community);
        $or->findOneBy(['type' => 1])->shouldBeCalled()->willReturn($placeType);

        $place->setName('Rzeczyca')->shouldBeCalled();
        $place->setType($placeType)->shouldBeCalled();
        $place->setCommunity($community)->shouldBeCalled();
        $place->setParentPlace($parentPlace)->shouldBeCalled();

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($place->getWrappedObject());
    }
}
