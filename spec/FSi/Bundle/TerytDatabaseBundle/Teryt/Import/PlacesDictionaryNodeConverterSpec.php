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
use FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleXMLElement;

// phpcs:disable PSR1.Methods.CamelCapsMethodName
class PlacesDictionaryNodeConverterSpec extends ObjectBehavior
{
    public function let(ObjectManager $om, ObjectRepository $or): void
    {
        // It is not possible to mock internal classes with final constructor
        $this->beConstructedWith(new SimpleXMLElement('<row></row>'), $om);
        $om->getRepository(Argument::type('string'))->willReturn($or);
        $or->findOneBy(Argument::type('array'))->willReturn();
    }

    public function it_converts_node_to_places_dictionary_entry(ObjectManager $om): void
    {
        $xml = <<<EOT
<row>
  <rm>02</rm>
  <nazwa_rm>kolonia                 </nazwa_rm>
  <stan_na>2013-02-28</stan_na>
</row>
EOT;

        $placeType = new PlaceType(2, 'kolonia');

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($placeType);
    }

    public function it_converts_node_to_places_dictionary_entry_with_updating_existing_one(
        ObjectManager $om,
        ObjectRepository $or,
        PlaceType $placeType
    ): void {
        $xml = <<<EOT
<row>
  <rm>02</rm>
  <nazwa_rm>kolonia                 </nazwa_rm>
  <stan_na>2013-02-28</stan_na>
</row>
EOT;

        $or->findOneBy(['type' => 2])->shouldBeCalled()->willReturn($placeType);

        $placeType->setName('kolonia')->shouldBeCalled();

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($placeType);
    }
}
