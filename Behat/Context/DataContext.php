<?php

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use FSi\Bundle\TerytDatabaseBundle\Entity\Community;
use FSi\Bundle\TerytDatabaseBundle\Entity\CommunityType;
use FSi\Bundle\TerytDatabaseBundle\Entity\District;
use FSi\Bundle\TerytDatabaseBundle\Entity\Place;
use FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType;
use FSi\Bundle\TerytDatabaseBundle\Entity\Province;
use FSi\Bundle\TerytDatabaseBundle\Entity\Street;
use Symfony\Component\HttpKernel\KernelInterface;

class DataContext implements KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var string
     */
    protected $lastCommandOutput;

    /**
     * Sets Kernel instance.
     *
     * @param KernelInterface $kernel HttpKernel instance
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^following province was already imported$/
     */
    public function followingProvinceWasAlreadyImported(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createProvince($row['Code'], $row['Name']);
        }
    }

    /**
     * @Given /^following district was already imported$/
     */
    public function followingDistrictWasAlreadyImported(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createDistrict($row['Code'], $row['Name'], $this->findProvinceByName($row['Province']));
        }
    }

    /**
     * @Given /^following places was already imported$/
     */
    public function followingPlacesWasAlreadyImported(TableNode $table)
    {
        $this->createPlaceType(1, 'fake');
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createPlace($row['Identity'], $row['Name'], 'fake', $row['Community']);
        }
    }

    /**
     * @Given /^following community was already imported$/
     */
    public function followingCommunityWasAlreadyImported(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createCommunity($row['Code'], $row['Name'], $row['Community type'], $row['District']);
        }
    }

    /**
     * @Then /^following place should exist in database$/
     */
    public function followingPlaceShouldExistInDatabase(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createPlace($row['Identity'], $row['Name'], $row['Place type'], $row['Community']);
        }
    }

    /**
     * @Then /^following places dictionary exist in database$/
     */
    public function followingPlacesDictionaryExistInDatabase(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createPlaceType($row['Type'], $row['Name']);
        }
    }

    /**
     * @Given /^following streets was already imported$/
     */
    public function followingStreetsWasAlreadyImported(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createStreet($row['Identity'], $row['Type'], $row['Name'], $row['Additional name'], $row['Place']);
        }
    }

    /**
     * @Given /^there is a community in database with code "([^"]*)" and name "([^"]*)" in district "([^"]*)"$/
     */
    public function thereIsACommunityInDatabaseWithCodeAndName($code, $name, $district)
    {
        $this->createCommunityType(1, 'fake');
        $this->createCommunity($code, $name, 'fake', $district);
    }

    /**
     * @Given /^there is a place type with type "([^"]*)" and name "([^"]*)"$/
     */
    public function thereIsAPlaceTypeWithTypeAndName($type, $name)
    {
        $placeType = new PlaceType();
        $placeType->setType($type)
            ->setName($name);

        $this->kernel->getContainer()->get('doctrine')->getManager()->persist($placeType);
        $this->kernel->getContainer()->get('doctrine')->getManager()->flush();
    }

    /**
     * @param $code
     * @param $name
     * @param $typeName
     * @param $districtName
     * @internal param $row
     */
    protected function createCommunity($code, $name, $typeName, $districtName)
    {
        $community = new Community();
        $community->setCode($code)
            ->setName($name)
            ->setType($this->findCommunityTypeByName($typeName))
            ->setDistrict($this->findDistrictByName($districtName));

        $this->kernel->getContainer()->get('doctrine')->getManager()->persist($community);
        $this->kernel->getContainer()->get('doctrine')->getManager()->flush();
    }

    protected function createCommunityType($type, $name)
    {
        $communityType = new CommunityType();
        $communityType->setType($type)
            ->setName($name);

        $this->kernel->getContainer()->get('doctrine')->getManager()->persist($communityType);
        $this->kernel->getContainer()->get('doctrine')->getManager()->flush();
    }

    protected function createPlace($id, $name, $typeName = null, $communityName = null)
    {
        $place = new Place();
        $place->setId($id)
            ->setName($name);

        if (isset($typeName)) {
            $place->setType($this->findPlaceTypeByName($typeName));
        }

        if (isset($communityName)) {
            $place->setCommunity($this->findCommunityByName($communityName));
        }

        $this->kernel->getContainer()->get('doctrine')->getManager()->persist($place);
        $this->kernel->getContainer()->get('doctrine')->getManager()->flush();
    }

    protected function createPlaceType($type, $name)
    {
        $placeType = new PlaceType();
        $placeType->setType($type)
            ->setName($name);

        $this->kernel->getContainer()->get('doctrine')->getManager()->persist($placeType);
        $this->kernel->getContainer()->get('doctrine')->getManager()->flush();
    }


    protected function createProvince($code, $name)
    {
        $provinceEntity = new Province();
        $provinceEntity->setCode($code)
            ->setName($name);

        $this->kernel->getContainer()->get('doctrine')->getManager()->persist($provinceEntity);
        $this->kernel->getContainer()->get('doctrine')->getManager()->flush();
    }


    protected function createDistrict($code, $name, Province $province)
    {
        $communityEntity = new District();
        $communityEntity->setCode($code)
            ->setName($name)
            ->setProvince($province);

        $this->kernel->getContainer()->get('doctrine')->getManager()->persist($communityEntity);
        $this->kernel->getContainer()->get('doctrine')->getManager()->flush();
    }

    /**
     * @param $id
     * @param $type
     * @param $name
     * @param $additionalName
     * @param $placeName
     * @internal param $row
     */
    private function createStreet($id, $type, $name, $additionalName, $placeName)
    {
        $street = new Street();
        $street->setId($id)
            ->setType($type)
            ->setName($name)
            ->setAdditionalName($additionalName)
            ->setPlace($this->findPlaceByName($placeName));

        $this->kernel->getContainer()->get('doctrine')->getManager()->persist($street);
        $this->kernel->getContainer()->get('doctrine')->getManager()->flush();
    }

    protected function findProvinceByName($name)
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:Province')
            ->findOneByName($name);
    }

    protected function findDistrictByName($name)
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:District')
            ->findOneByName($name);
    }

    protected function findCommunityByName($name)
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:Community')
            ->findOneByName($name);
    }

    protected function findCommunityTypeByName($name)
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:CommunityType')
            ->findOneByName($name);
    }

    protected function findPlaceTypeByName($name)
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:PlaceType')
            ->findOneByName($name);
    }

    protected function findPlaceByName($name)
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:Place')
            ->findOneByName($name);
    }
}