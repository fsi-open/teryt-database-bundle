<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
    <woj>02</woj>
    <pow>23</pow>
    <gmi>09</gmi>
    <rodz_gmi>2</rodz_gmi>
    <sym>0884849</sym>
    <sym_ul>10268</sym_ul>
    <cecha>ul.</cecha>
    <nazwa_1>Księżycowa </nazwa_1>
    <nazwa_2>
    </nazwa_2>
    <stan_na>2013-10-10</stan_na>
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
    <woj>02</woj>
    <pow>23</pow>
    <gmi>09</gmi>
    <rodz_gmi>2</rodz_gmi>
    <sym>0884849</sym>
    <sym_ul>10268</sym_ul>
    <cecha>ul.</cecha>
    <nazwa_1>Księżycowa </nazwa_1>
    <nazwa_2>
    </nazwa_2>
    <stan_na>2013-10-10</stan_na>
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
