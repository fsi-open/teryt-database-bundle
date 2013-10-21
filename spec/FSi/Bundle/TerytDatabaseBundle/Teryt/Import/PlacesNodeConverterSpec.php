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

        $community = new Community();
        $community->setCode('041105');

        $placeType = new Type();
        $placeType->setName('miasto')
            ->setType('01');

        $or->findOneBy(array(
            'code' => '041105'
        ))->shouldBeCalled()->willReturn($community);

        $or->findOneBy(array(
            'type' => '01'
        ))->shouldBeCalled()->willReturn($placeType);

        $place = new Place();
        $place->setName('Rzeczyca')
            ->setId('0867650')
            ->setType($placeType)
            ->setCommunity($community);

        $this->beConstructedWith(new \SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($place);
    }
}
