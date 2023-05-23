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
use FSi\Bundle\TerytDatabaseBundle\Entity\Province;
use FSi\Bundle\TerytDatabaseBundle\Exception\TerritorialDivisionNodeConverterException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleXMLElement;

// phpcs:disable PSR1.Methods.CamelCapsMethodName
class TerritorialDivisionNodeConverterSpec extends ObjectBehavior
{
    public function let(ObjectManager $om, ObjectRepository $or): void
    {
        // It is not possible to mock internal classes with final constructor
        $this->beConstructedWith(new SimpleXMLElement('<row></row>'), $om);
        $om->getRepository(Argument::type('string'))->willReturn($or);
        $or->findOneBy(Argument::type('array'))->willReturn();
    }

    public function it_converts_node_to_province(ObjectManager $om): void
    {
        $xml = <<<EOT
<row>
    <woj>02</woj>
    <pow>
    </pow>
    <gmi>
    </gmi>
    <rodz>
    </rodz>
    <nazwa>DOLNOŚLĄSKIE</nazwa>
    <nazdod>województwo</nazdod>
    <stan_na>2013-01-01</stan_na>
</row>
EOT;

        $expectedProvince = new Province(2, 'województwo');
        $expectedProvince->setName('DOLNOŚLĄSKIE');

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($expectedProvince);
    }

    public function it_converts_node_to_province_with_updating_existing_one(
        ObjectManager $om,
        ObjectRepository $or,
        Province $province
    ): void {
        $xml = <<<EOT
<row>
    <woj>02</woj>
    <pow>
    </pow>
    <gmi>
    </gmi>
    <rodz>
    </rodz>
    <nazwa>Dolnośląskie</nazwa>
    <nazdod>województwo</nazdod>
    <stan_na>2013-01-01</stan_na>
</row>
EOT;
        $om->getRepository(Province::class)
            ->shouldBeCalled()
            ->willReturn($or);

        $or->findOneBy(['code' => 2])->shouldBeCalled()->willReturn($province);

        $province->setName('Dolnośląskie')->shouldBeCalled();

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($province);
    }

    public function it_converts_node_to_district(ObjectManager $om, ObjectRepository $or): void
    {
        $xml = <<<EOT
<row>
    <woj>02</woj>
    <pow>01</pow>
    <gmi>
    </gmi>
    <rodz>
    </rodz>
    <nazwa>bolesławiecki</nazwa>
    <nazdod>powiat</nazdod>
    <stan_na>2013-01-01</stan_na>
</row>
EOT;
        $province = new Province(2, 'województwo');
        $or->findOneBy(['code' => 2])->shouldBeCalled()->willReturn($province);

        $expectedDistrict = new District($province, 201, 'bolesławiecki');

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($expectedDistrict);
    }

    public function it_converts_node_to_district_with_updating_existing_one(
        ObjectManager $om,
        ObjectRepository $or,
        District $district
    ): void {
        $xml = <<<EOT
<row>
    <woj>02</woj>
    <pow>01</pow>
    <gmi>
    </gmi>
    <rodz>
    </rodz>
    <nazwa>bolesławiecki</nazwa>
    <nazdod>powiat</nazdod>
    <stan_na>2013-01-01</stan_na>
</row>
EOT;

        $or->findOneBy(['code' => 201])->shouldBeCalled()->willReturn($district);

        $province = new Province(2, 'województwo');

        $or->findOneBy(['code' => 2])->shouldBeCalled()->willReturn($province);

        $district->setName('bolesławiecki')->shouldBeCalled();

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($district);
    }

    public function it_converts_node_to_community(ObjectManager $om, ObjectRepository $or): void
    {
        $xml = <<<EOT
<row>
    <woj>02</woj>
    <pow>01</pow>
    <gmi>01</gmi>
    <rodz>1</rodz>
    <nazwa>Bolesławiec</nazwa>
    <nazdod>gmina miejska</nazdod>
    <stan_na>2013-01-01</stan_na>
</row>
EOT;
        $district = new District(new Province(1, 'województwo'), 201, 'Bolesławiec');

        $communityType = new CommunityType(1, 'gmina miejska');

        $or->findOneBy(['code' => 201])->shouldBeCalled()->willReturn($district);
        $or->findOneBy(['type' => 1])->shouldBeCalled()->willReturn($communityType);

        $expectedCommunity = new Community($district, 201011, 'Bolesławiec', $communityType);

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($expectedCommunity);
    }

    public function it_converts_node_to_community_with_updating_existing_one(
        ObjectManager $om,
        ObjectRepository $or,
        Community $community
    ): void {
        $xml = <<<EOT
<row>
    <woj>02</woj>
    <pow>01</pow>
    <gmi>01</gmi>
    <rodz>1</rodz>
    <nazwa>Bolesławiec</nazwa>
    <nazdod>gmina miejska</nazdod>
    <stan_na>2013-01-01</stan_na>
</row>
EOT;
        $district = new District(new Province(1, 'województwo'), 201, 'Bolesławiec');

        $communityType = new CommunityType(1, 'gmina miejska');

        $or->findOneBy(['code' => 201])->shouldBeCalled()->willReturn($district);
        $or->findOneBy(['type' => 1])->shouldBeCalled()->willReturn($communityType);
        $or->findOneBy(['code' => 201011])->shouldBeCalled()->willReturn($community);

        $community->setName('Bolesławiec')->shouldBeCalled();
        $community->setType($communityType)->shouldBeCalled();

        $this->beConstructedWith(new SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($community);
    }

    public function it_throws_exception_when_cant_convert_node_to_entity(ObjectManager $om): void
    {
        $this->beConstructedWith(new SimpleXMLElement('<row></row>'), $om);

        $exception = new TerritorialDivisionNodeConverterException('Unknown territory type');
        $this->shouldThrow($exception)->during('convertToEntity', []);
    }
}
