<?php

namespace spec\FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use FSi\Bundle\TerytDatabaseBundle\Entity\Place;
use FSi\Bundle\TerytDatabaseBundle\Entity\Street;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StreetsNodeConverterSpec extends ObjectBehavior
{
    function let(ObjectManager $om, ObjectRepository $or)
    {
        // It is not possible to mock internal classes with final constructor
        $this->beConstructedWith(new \SimpleXMLElement('<row></row>'), $om);
        $om->getRepository(Argument::type('string'))->willReturn($or);
        $or->findOneBy(Argument::type('array'))->willReturn();
    }

    function it_converts_node_to_street_entry(ObjectManager $om, ObjectRepository $or)
    {
        $xml = <<<EOT
<row>
    <col name="WOJ">02</col>
    <col name="POW">23</col>
    <col name="GMI">09</col>
    <col name="RODZ_GMI">2</col>
    <col name="SYM">0884849</col>
    <col name="SYM_UL">10268</col>
    <col name="CECHA">ul.</col>
    <col name="NAZWA_1">Księżycowa </col>
    <col name="NAZWA_2"/>
    <col name="STAN_NA">2013-10-10</col>
</row>
EOT;
        $place = new Place(884849);
        $place->setName('City');

        $or->findOneBy(array('id' => 884849))
            ->shouldBeCalled()
            ->willReturn($place);

        $street = new Street($place, 10268);
        $street->setName('Księżycowa')
            ->setAdditionalName('')
            ->setType('ul.');

        $this->beConstructedWith(new \SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($street);
    }

    function it_converts_node_to_street_entry_with_updating_existing_one(
        ObjectManager $om, ObjectRepository $or, Street $street
    ) {
        $xml = <<<EOT
<row>
    <col name="WOJ">02</col>
    <col name="POW">23</col>
    <col name="GMI">09</col>
    <col name="RODZ_GMI">2</col>
    <col name="SYM">0884849</col>
    <col name="SYM_UL">10268</col>
    <col name="CECHA">ul.</col>
    <col name="NAZWA_1">Księżycowa </col>
    <col name="NAZWA_2"/>
    <col name="STAN_NA">2013-10-10</col>
</row>
EOT;
        $place = new Place(884849);
        $place->setName('City');

        $or->findOneBy(array('id' => '0884849'))
            ->shouldBeCalled()
            ->willReturn($place);

        $or->findOneBy(array('id' => '10268', 'place' => $place))
            ->shouldBeCalled()
            ->willReturn($street);

        $street->setName('Księżycowa')->shouldBeCalled()->willReturn($street);
        $street->setAdditionalName(null)->shouldBeCalled()->willReturn($street);
        $street->setType('ul.')->shouldBeCalled()->willReturn($street);

        $this->beConstructedWith(new \SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($street->getWrappedObject());
    }
}
