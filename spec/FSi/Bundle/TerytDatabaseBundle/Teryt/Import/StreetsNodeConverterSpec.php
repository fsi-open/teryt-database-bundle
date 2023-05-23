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
use FSi\Bundle\TerytDatabaseBundle\Entity\Street;
use FSi\Bundle\TerytDatabaseBundle\Model\Place\Type;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleXMLElement;

// phpcs:disable PSR1.Methods.CamelCapsMethodName
class StreetsNodeConverterSpec extends ObjectBehavior
{
    public function let(ObjectManager $om, ObjectRepository $or): void
    {
        // It is not possible to mock internal classes with final constructor
        $this->beConstructedWith(new SimpleXMLElement('<row></row>'), $om);
        $om->getRepository(Argument::type('string'))->willReturn($or);
        $or->findOneBy(Argument::type('array'))->willReturn();
    }

    public function it_converts_node_to_street_entry(ObjectManager $om, ObjectRepository $or): void
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
        $community = new Community(
            new District(new Province(1, 'województwo'), 1, 'Powiat'),
            1,
            'Gmina',
            new CommunityType(1, 'Gmina wiejska')
        );
        $place = new Place(884849, 'City', new PlaceType(1, 'wieś'), $community);

        $or->findOneBy(['id' => 884849])->shouldBeCalled()->willReturn($place);

        $street = new Street($place, 10268, 'ul.', null, 'Księżycowa');

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($street);
    }

    public function it_converts_node_to_street_entry_with_updating_existing_one(
        ObjectManager $om,
        ObjectRepository $or,
        Street $street
    ): void {
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
        $community = new Community(
            new District(new Province(1, 'województwo'), 1, 'Powiat'),
            1,
            'Gmina',
            new CommunityType(1, 'Gmina wiejska')
        );
        $place = new Place(884849, 'City', new Type(1, 'wieś'), $community);

        $or->findOneBy(['id' => '0884849'])->shouldBeCalled()->willReturn($place);
        $or->findOneBy(['id' => '10268', 'place' => $place])->shouldBeCalled()->willReturn($street);

        $street->setName('Księżycowa')->shouldBeCalled();
        $street->setAdditionalName(null)->shouldBeCalled();
        $street->setType('ul.')->shouldBeCalled();

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($street->getWrappedObject());
    }
}
