<?php

namespace spec\FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use FSi\Bundle\TerytDatabaseBundle\Entity\Community;
use FSi\Bundle\TerytDatabaseBundle\Entity\Place;
use FSi\Bundle\TerytDatabaseBundle\Model\Place\Type;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PlacesNodeConverterSpec extends ObjectBehavior
{
    function let(ObjectManager $om, ObjectRepository $or)
    {
        // It is not possible to mock internal classes with final constructor
        $this->beConstructedWith(new \SimpleXMLElement('<row></row>'), $om);
        $om->getRepository(Argument::type('string'))->willReturn($or);
        $or->findOneBy(Argument::type('array'))->willReturn();
    }

    function it_converts_node_to_place_entry(ObjectManager $om, ObjectRepository $or)
    {
        $xml = <<<EOT
<row>
    <col name="WOJ">04</col>
    <col name="POW">11</col>
    <col name="GMI">05</col>
    <col name="RODZ_GMI">5</col>
    <col name="RM">01</col>
    <col name="MZ">1</col>
    <col name="NAZWA">Rzeczyca</col>
    <col name="SYM">0867650</col>
    <col name="SYMPOD">0867650</col>
    <col name="STAN_NA">2013-03-06</col>
</row>
EOT;

        $community = new Community(41105);

        $placeType = new Type(1);
        $placeType->setName('miasto');

        $or->findOneBy(array(
            'code' => 411055
        ))->shouldBeCalled()->willReturn($community);

        $or->findOneBy(array(
            'type' => 1
        ))->shouldBeCalled()->willReturn($placeType);

        $place = new Place(867650);
        $place->setName('Rzeczyca')
            ->setType($placeType)
            ->setCommunity($community);

        $this->beConstructedWith(new \SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($place);
    }

    function it_converts_node_to_place_with_updating_existing_one(
        ObjectManager $om, ObjectRepository $or, Place $place
    ) {
        $xml = <<<EOT
<row>
    <col name="WOJ">04</col>
    <col name="POW">11</col>
    <col name="GMI">05</col>
    <col name="RODZ_GMI">5</col>
    <col name="RM">01</col>
    <col name="MZ">1</col>
    <col name="NAZWA">Rzeczyca</col>
    <col name="SYM">0867650</col>
    <col name="SYMPOD">0867650</col>
    <col name="STAN_NA">2013-03-06</col>
</row>
EOT;

        $community = new Community(41105);

        $placeType = new Type(1);
        $placeType->setName('miasto');

        $or->findOneBy(array(
            'id' => 867650
        ))->shouldBeCalled()->willReturn($place);

        $or->findOneBy(array(
            'code' => 411055
        ))->shouldBeCalled()->willReturn($community);

        $or->findOneBy(array(
            'type' => '01'
        ))->shouldBeCalled()->willReturn($placeType);

        $place->setName('Rzeczyca')->shouldBeCalled()->willReturn($place);
        $place->setType($placeType)->shouldBeCalled()->willReturn($place);
        $place->setCommunity($community)->shouldBeCalled()->willReturn($place);
        $place->setParentPlace(null)->shouldBeCalled()->willReturn($place);

        $this->beConstructedWith(new \SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($place->getWrappedObject());
    }

    function it_updates_parent_place_in_existing_place(
        ObjectManager $om, ObjectRepository $or, Place $place, Place $parentPlace
    ) {
        $xml = <<<EOT
<row>
    <col name="WOJ">04</col>
    <col name="POW">11</col>
    <col name="GMI">05</col>
    <col name="RODZ_GMI">5</col>
    <col name="RM">01</col>
    <col name="MZ">1</col>
    <col name="NAZWA">Rzeczyca</col>
    <col name="SYM">0867650</col>
    <col name="SYMPOD">0867643</col>
    <col name="STAN_NA">2013-03-06</col>
</row>
EOT;

        $community = new Community(41105);

        $placeType = new Type(1);
        $placeType->setName('miasto');

        $or->findOneBy(array(
            'id' => 867650
        ))->shouldBeCalled()->willReturn($place);

        $or->findOneBy(array(
            'id' => 867643
        ))->shouldBeCalled()->willReturn($parentPlace);

        $or->findOneBy(array(
            'code' => 411055
        ))->shouldBeCalled()->willReturn($community);

        $or->findOneBy(array(
            'type' => 1
        ))->shouldBeCalled()->willReturn($placeType);

        $place->setName('Rzeczyca')->shouldBeCalled()->willReturn($place);
        $place->setType($placeType)->shouldBeCalled()->willReturn($place);
        $place->setCommunity($community)->shouldBeCalled()->willReturn($place);
        $place->setParentPlace($parentPlace)->shouldBeCalled()->willReturn($place);

        $this->beConstructedWith(new \SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($place->getWrappedObject());
    }
}
