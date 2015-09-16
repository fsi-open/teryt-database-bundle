<?php

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ImportTerytCommandContext implements KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var string
     */
    protected $fixturesPath;

    function __construct($fixturesPath)
    {
        $this->fixturesPath = $fixturesPath;
    }

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
     * @Given /^"([^"]*)" file have following content:$/
     */
    public function xmlFileHaveFollowingContent($fileName, PyStringNode $fileContent)
    {
        $targetFolder = sprintf("%s/Project/app/teryt", $this->fixturesPath);
        if (!file_exists($targetFolder)) {
            mkdir($targetFolder);
        }

        $filePath = sprintf("%s/%s", $targetFolder, $fileName);
        file_put_contents($filePath, $fileContent->getRaw());
        expect(file_exists($filePath))->toBe(true);
    }

    /**
     * @Given /^there are no provinces in database$/
     */
    public function thereAreNoProvincesInDatabase()
    {
        expect($this->getProvinceRepository()
            ->findAll())->toBe(array());
    }

    /**
     * @Given /^there are no districts in database$/
     */
    public function thereAreNoDistrictsInDatabase()
    {
        expect($this->getDistrictRepository()
            ->findAll())->toBe(array());
    }

    /**
     * @Given /^there are no communities in database$/
     */
    public function thereAreNoCommunitiesInDatabase()
    {
        expect($this->getCommunityRepository()
            ->findAll())->toBe(array());
    }

    /**
     * @Given /^places dictionary table in database is empty$/
     */
    public function placesDictionaryTableInDatabaseIsEmpty()
    {
        expect($this->getPlaceTypeRepository()
            ->findAll())->toBe(array());
    }

    /**
     * @Given /^places table in database is empty$/
     */
    public function placesTableInDatabaseIsEmpty()
    {
        expect($this->getPlaceRepository()
            ->findAll())->toBe(array());
    }

    /**
     * @Given /^there are no streets in database$/
     */
    public function thereAreNoStreetsInDatabase()
    {
        expect($this->getStreetRepository()
            ->findAll())->toBe(array());
    }

    /**
     * @Then /^following province should exist in database$/
     */
    public function followingProvinceShouldExistInDatabase(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $entity = $this->getProvinceRepository()->findOneByCode($row['Code']);
            expect($entity)->toBeAnInstanceOf('FSi\Bundle\TerytDatabaseBundle\Entity\Province');
            expect($entity->getName())->toBe($row['Name']);
        }
    }

    /**
     * @Then /^following district should exist in database$/
     */
    public function followingDistrictShouldExistInDatabase(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $entity = $this->getDistrictRepository()->findOneByCode($row['Code']);
            expect($entity)->toBeAnInstanceOf('FSi\Bundle\TerytDatabaseBundle\Entity\District');
            expect($entity->getName())->toBe($row['Name']);
            expect($entity->getProvince()->getName())->toBe($row['Province']);
        }
    }

    /**
     * @Then /^following communities should exist in database$/
     */
    public function followingCommunitiesShouldExistInDatabase(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $entity = $this->getCommunityRepository()->findOneByCode($row['Code']);
            expect($entity)->toBeAnInstanceOf('FSi\Bundle\TerytDatabaseBundle\Entity\Community');
            expect($entity->getName())->toBe($row['Name']);
            expect($entity->getDistrict()->getName())->toBe($row['District']);
            expect($entity->getType()->getName())->toBe($row['Community type']);
        }
    }

    /**
     * @Then /^following community types should exist in database$/
     */
    public function followingCommunityTypesShouldExistInDatabase(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $entity = $this->getCommunityTypeRepository()->findOneByType($row['Type']);
            expect($entity)->toBeAnInstanceOf('FSi\Bundle\TerytDatabaseBundle\Entity\CommunityType');
            expect($entity->getName())->toBe($row['Name']);
        }
    }

    /**
     * @Then /^places dictionary table in database should have following records$/
     */
    public function placesDictionaryTableInDatabaseShouldHaveFollowingRecords(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $entity = $this->getPlaceTypeRepository()->findOneByType($row['Type']);
            expect($entity)->toBeAnInstanceOf('FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType');
            expect($entity->getName())->toBe($row['Name']);
        }
    }

    /**
     * @Then /^places table in database should have following records$/
     */
    public function placesTableInDatabaseShouldHaveFollowingRecords(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $entity = $this->getPlaceRepository()->findOneById($row['Identity']);
            expect($entity)->toBeAnInstanceOf('FSi\Bundle\TerytDatabaseBundle\Entity\Place');
            expect($entity->getName())->toBe($row['Name']);
            expect($entity->getType()->getName())->toBe($row['Place type']);
            expect($entity->getCommunity()->getName())->toBe($row['Community']);
        }
    }

    /**
     * @Then /^following streets should exist in database$/
     */
    public function followingStreetsShouldExistInDatabase(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $entity = $this->getStreetRepository()->findOneById($row['Identity']);
            expect($entity)->toBeAnInstanceOf('FSi\Bundle\TerytDatabaseBundle\Entity\Street');
            expect($entity->getId())->toBe((int) $row['Identity']);
            expect($entity->getName())->toBe($row['Name']);
            expect($entity->getType())->toBe($row['Type']);
            expect($entity->getAdditionalName())->toBe($row['Additional name']);
            expect($entity->getPlace()->getName())->toBe($row['Place']);
        }
    }

    /**
     * @return mixed
     */
    private function getProvinceRepository()
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:Province');
    }

    /**
     * @return mixed
     */
    private function getDistrictRepository()
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:District');
    }

    /**
     * @return mixed
     */
    private function getCommunityRepository()
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:Community');
    }

    /**
     * @return mixed
     */
    private function getPlaceTypeRepository()
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:PlaceType');
    }

    /**
     * @return mixed
     */
    private function getCommunityTypeRepository()
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:CommunityType');
    }

    private function getPlaceRepository()
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:Place');
    }

    private function getStreetRepository()
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:Street');
    }
}