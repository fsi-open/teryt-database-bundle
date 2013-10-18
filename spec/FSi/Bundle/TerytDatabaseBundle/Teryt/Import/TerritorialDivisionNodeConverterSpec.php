<?php

namespace spec\FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use FSi\Bundle\TerytDatabaseBundle\Entity\Community;
use FSi\Bundle\TerytDatabaseBundle\Entity\District;
use FSi\Bundle\TerytDatabaseBundle\Entity\Province;
use FSi\Bundle\TerytDatabaseBundle\Exception\TerritorialDivisionNodeConverterException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TerritorialDivisionNodeConverterSpec extends ObjectBehavior
{
    function let(ObjectManager $om, ObjectRepository $or)
    {
        // It is not possible to mock internal classes with final constructor
        $this->beConstructedWith(new \SimpleXMLElement('<row></row>'), $om);
        $om->getRepository(Argument::type('string'))->willReturn($or);
    }

    function it_converts_node_to_province(ObjectManager $om)
    {
        $xml = <<<EOT
<row>
    <col name="WOJ">02</col>
    <col name="POW"/>
    <col name="GMI"/>
    <col name="RODZ"/>
    <col name="NAZWA">DOLNOŚLĄSKIE</col>
    <col name="NAZDOD">województwo</col>
    <col name="STAN_NA">2013-01-01</col>
</row>
EOT;

        $expectedProvince = new Province();
        $expectedProvince->setCode('02')
            ->setName('DOLNOŚLĄSKIE');

        $this->beConstructedWith(new \SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($expectedProvince);
    }

    function it_converts_node_to_district(ObjectManager $om, ObjectRepository $or)
    {
        $xml = <<<EOT
<row>
    <col name="WOJ">02</col>
    <col name="POW">01</col>
    <col name="GMI"/>
    <col name="RODZ"/>
    <col name="NAZWA">bolesławiecki</col>
    <col name="NAZDOD">powiat</col>
    <col name="STAN_NA">2013-01-01</col>
</row>
EOT;
        $province = new Province();
        $province->setCode('02');
        $or->findOneBy(array(
            'code' => '02'
        ))->shouldBeCalled()->willReturn($province);

        $expectedDistrict = new District();
        $expectedDistrict->setCode('0201')
            ->setName('bolesławiecki')
            ->setProvince($province);

        $this->beConstructedWith(new \SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($expectedDistrict);
    }

    function it_converts_node_to_community(ObjectManager $om, ObjectRepository $or)
    {
        $xml = <<<EOT
<row>
    <col name="WOJ">02</col>
    <col name="POW">01</col>
    <col name="GMI">01</col>
    <col name="RODZ">1</col>
    <col name="NAZWA">Bolesławiec</col>
    <col name="NAZDOD">gmina miejska</col>
    <col name="STAN_NA">2013-01-01</col>
</row>
EOT;
        $district = new District();
        $district->setCode('0201')
            ->setName('Bolesławiec');

        $or->findOneBy(array(
            'code' => '0201'
        ))->shouldBeCalled()->willReturn($district);


        $expectedCommunity = new Community();
        $expectedCommunity->setCode('020101')
            ->setName('Bolesławiec')
            ->setDistrict($district);

        $this->beConstructedWith(new \SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($expectedCommunity);
    }

    function it_throws_exception_when_cant_convert_node_to_entity(ObjectManager $om)
    {
        $this->beConstructedWith(new \SimpleXMLElement('<row></row>'), $om);

        $exception = new TerritorialDivisionNodeConverterException();
        $this->shouldThrow($exception)->during('convertToEntity', array());
    }
}
