<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Assert\Assertion;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use FSi\Bundle\TerytDatabaseBundle\Entity\Community;
use FSi\Bundle\TerytDatabaseBundle\Entity\CommunityType;
use FSi\Bundle\TerytDatabaseBundle\Entity\District;
use FSi\Bundle\TerytDatabaseBundle\Entity\Place;
use FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType;
use FSi\Bundle\TerytDatabaseBundle\Entity\Province;
use FSi\Bundle\TerytDatabaseBundle\Entity\Street;
use Symfony\Component\HttpKernel\KernelInterface;

class DataContext implements Context
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var string
     */
    protected $lastCommandOutput;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^following province was already imported$/
     */
    public function followingProvinceWasAlreadyImported(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createProvince((int) $row['Code'], $row['Name']);
        }
    }

    /**
     * @Given /^following district was already imported$/
     */
    public function followingDistrictWasAlreadyImported(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createDistrict((int) $row['Code'], $row['Name'], $this->findProvinceByName($row['Province']));
        }
    }

    /**
     * @Given /^following places was already imported$/
     */
    public function followingPlacesWasAlreadyImported(TableNode $table): void
    {
        $this->createPlaceType(1, 'fake');
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createPlace((int) $row['Identity'], $row['Name'], 'fake', $row['Community']);
        }
    }

    /**
     * @Given /^following community was already imported$/
     */
    public function followingCommunityWasAlreadyImported(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createCommunity((int) $row['Code'], $row['Name'], $row['Community type'], $row['District']);
        }
    }

    /**
     * @Then /^following place should exist in database$/
     */
    public function followingPlaceShouldExistInDatabase(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createPlace((int) $row['Identity'], $row['Name'], $row['Place type'], $row['Community']);
        }
    }

    /**
     * @Then /^following places dictionary exist in database$/
     */
    public function followingPlacesDictionaryExistInDatabase(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createPlaceType((int) $row['Type'], $row['Name']);
        }
    }

    /**
     * @Given /^following streets was already imported$/
     */
    public function followingStreetsWasAlreadyImported(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $this->createStreet(
                (int) $row['Identity'],
                $row['Type'],
                $row['Name'],
                $row['Additional name'] ?: null,
                $row['Place']
            );
        }
    }

    /**
     * @Given /^there is a community in database with code "([^"]*)" and name "([^"]*)" in district "([^"]*)"$/
     */
    public function thereIsACommunityInDatabaseWithCodeAndName(string $code, string $name, string $district): void
    {
        $this->createCommunityType(1, 'fake');
        $this->createCommunity((int) $code, $name, 'fake', $district);
    }

    /**
     * @Given /^there is a place type with type "([^"]*)" and name "([^"]*)"$/
     */
    public function thereIsAPlaceTypeWithTypeAndName(string $type, string $name): void
    {
        $placeType = new PlaceType((int) $type, $name);

        $this->getManager()->persist($placeType);
        $this->getManager()->flush();
    }

    protected function createCommunity(int $code, string $name, string $typeName, string $districtName): void
    {
        $community = new Community(
            $this->findDistrictByName($districtName),
            $code,
            $name,
            $this->findCommunityTypeByName($typeName)
        );

        $this->getManager()->persist($community);
        $this->getManager()->flush();
    }

    protected function createCommunityType(int $type, string $name): void
    {
        $communityType = new CommunityType($type, $name);

        $this->getManager()->persist($communityType);
        $this->getManager()->flush();
    }

    protected function createPlace(int $id, string $name, string $typeName, string $communityName): void
    {
        $type = $this->findPlaceTypeByName($typeName);
        $community = $this->findCommunityByName($communityName);

        $place = new Place($id, $name, $type, $community);

        $this->getManager()->persist($place);
        $this->getManager()->flush();
    }

    protected function createPlaceType(int $type, string $name): void
    {
        $placeType = new PlaceType($type, $name);

        $this->getManager()->persist($placeType);
        $this->getManager()->flush();
    }

    protected function createProvince(int $code, string $name): void
    {
        $provinceEntity = new Province($code, $name);

        $this->getManager()->persist($provinceEntity);
        $this->getManager()->flush();
    }

    protected function createDistrict(int $code, string $name, Province $province): void
    {
        $communityEntity = new District($province, $code, $name);

        $this->getManager()->persist($communityEntity);
        $this->getManager()->flush();
    }

    private function createStreet(int $id, string $type, string $name, ?string $additionalName, string $placeName): void
    {
        $street = new Street($this->findPlaceByName($placeName), $id, $type, $additionalName, $name);

        $this->getManager()->persist($street);
        $this->getManager()->flush();
    }

    protected function findProvinceByName(string $name): ?Province
    {
        return $this->getManager()
            ->getRepository(Province::class)
            ->findOneBy(['name' => $name]);
    }

    protected function findDistrictByName(string $name): ?District
    {
        return $this->getManager()
            ->getRepository(District::class)
            ->findOneBy(['name' => $name]);
    }

    protected function findCommunityByName(string $name): ?Community
    {
        return $this->getManager()
            ->getRepository(Community::class)
            ->findOneBy(['name' => $name]);
    }

    protected function findCommunityTypeByName(string $name): ?CommunityType
    {
        return $this->getManager()
            ->getRepository(CommunityType::class)
            ->findOneBy(['name' => $name]);
    }

    protected function findPlaceTypeByName(string $name): ?PlaceType
    {
        return $this->getManager()
            ->getRepository(PlaceType::class)
            ->findOneBy(['name' => $name]);
    }

    protected function findPlaceByName(string $name): ?Place
    {
        return $this->getManager()
            ->getRepository(Place::class)
            ->findOneBy(['name' => $name]);
    }

    private function getManager(): EntityManagerInterface
    {
        $managerRegistry = $this->kernel->getContainer()->get(ManagerRegistry::class);
        Assertion::isInstanceOf($managerRegistry, ManagerRegistry::class);

        $manager = $managerRegistry->getManager();
        Assertion::isInstanceOf($manager, EntityManagerInterface::class);

        return $manager;
    }
}
