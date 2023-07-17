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
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use FSi\Bundle\TerytDatabaseBundle\Entity\District;
use FSi\Bundle\TerytDatabaseBundle\Entity\Place;
use FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType;
use FSi\Bundle\TerytDatabaseBundle\Entity\Province;
use FSi\Bundle\TerytDatabaseBundle\Entity\Street;
use Symfony\Component\HttpKernel\KernelInterface;
use FSi\Bundle\TerytDatabaseBundle\Entity\Community;
use FSi\Bundle\TerytDatabaseBundle\Entity\CommunityType;

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

    public function __construct(string $fixturesPath)
    {
        $this->fixturesPath = $fixturesPath;
    }

    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^"([^"]*)" file have following content:$/
     */
    public function xmlFileHaveFollowingContent(string $fileName, PyStringNode $fileContent): void
    {
        $targetFolder = sprintf('%s/teryt', $this->fixturesPath);
        if (!file_exists($targetFolder) && !mkdir($targetFolder) && !is_dir($targetFolder)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $targetFolder));
        }

        $filePath = sprintf('%s/%s', $targetFolder, $fileName);
        file_put_contents($filePath, $fileContent->getRaw());
        Assertion::true(file_exists($filePath));
    }

    /**
     * @Given /^there are no provinces in database$/
     */
    public function thereAreNoProvincesInDatabase(): void
    {
        Assertion::same($this->getProvinceRepository()->findAll(), []);
    }

    /**
     * @Given /^there are no districts in database$/
     */
    public function thereAreNoDistrictsInDatabase(): void
    {
        Assertion::same($this->getDistrictRepository()->findAll(), []);
    }

    /**
     * @Given /^there are no communities in database$/
     */
    public function thereAreNoCommunitiesInDatabase(): void
    {
        Assertion::same($this->getCommunityRepository()->findAll(), []);
    }

    /**
     * @Given /^places dictionary table in database is empty$/
     */
    public function placesDictionaryTableInDatabaseIsEmpty(): void
    {
        Assertion::same($this->getPlaceTypeRepository()->findAll(), []);
    }

    /**
     * @Given /^places table in database is empty$/
     */
    public function placesTableInDatabaseIsEmpty(): void
    {
        Assertion::same($this->getPlaceRepository()->findAll(), []);
    }

    /**
     * @Given /^there are no streets in database$/
     */
    public function thereAreNoStreetsInDatabase(): void
    {
        Assertion::same($this->getStreetRepository()->findAll(), []);
    }

    /**
     * @Then /^following province should exist in database$/
     */
    public function followingProvinceShouldExistInDatabase(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            /** @var Province|null $entity */
            $entity = $this->getProvinceRepository()->findOneBy(['code' => $row['Code']]);
            Assertion::isInstanceOf($entity, Province::class);
            Assertion::same($entity->getName(), $row['Name']);
        }
    }

    /**
     * @Then /^following district should exist in database$/
     */
    public function followingDistrictShouldExistInDatabase(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            /** @var District|null $entity */
            $entity = $this->getDistrictRepository()->findOneBy(['code' => $row['Code']]);
            Assertion::isInstanceOf($entity, District::class);
            Assertion::same($entity->getName(), $row['Name']);
            Assertion::same($entity->getProvince()->getName(), $row['Province']);
        }
    }

    /**
     * @Then /^following communities should exist in database$/
     */
    public function followingCommunitiesShouldExistInDatabase(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            /** @var Community|null $entity */
            $entity = $this->getCommunityRepository()->findOneBy(['code' => $row['Code']]);
            Assertion::isInstanceOf($entity, Community::class);
            Assertion::same($entity->getName(), $row['Name']);
            Assertion::same($entity->getDistrict()->getName(), $row['District']);
            Assertion::same($entity->getType()->getName(), $row['Community type']);
        }
    }

    /**
     * @Then /^following community types should exist in database$/
     */
    public function followingCommunityTypesShouldExistInDatabase(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            /** @var CommunityType|null $entity */
            $entity = $this->getCommunityTypeRepository()->findOneBy(['type' => $row['Type']]);
            Assertion::isInstanceOf($entity, CommunityType::class);
            Assertion::same($entity->getName(), $row['Name']);
        }
    }

    /**
     * @Then /^places dictionary table in database should have following records$/
     */
    public function placesDictionaryTableInDatabaseShouldHaveFollowingRecords(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            /** @var PlaceType|null $entity */
            $entity = $this->getPlaceTypeRepository()->findOneBy(['type' => $row['Type']]);
            Assertion::isInstanceOf($entity, PlaceType::class);
            Assertion::same($entity->getName(), $row['Name']);
        }
    }

    /**
     * @Then /^places table in database should have following records$/
     */
    public function placesTableInDatabaseShouldHaveFollowingRecords(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            /** @var Place|null $entity */
            $entity = $this->getPlaceRepository()->find($row['Identity']);
            Assertion::isInstanceOf($entity, Place::class);
            Assertion::same($entity->getName(), $row['Name']);
            Assertion::same($entity->getType()->getName(), $row['Place type']);
            Assertion::same($entity->getCommunity()->getName(), $row['Community']);
            if (!empty($row['Parent place'])) {
                Assertion::same($entity->getParentPlace()->getName(), $row['Parent place']);
            }
        }
    }

    /**
     * @Then /^following streets should exist in database$/
     */
    public function followingStreetsShouldExistInDatabase(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $entity = $this->getStreetRepository()->findOneBy(['id' => $row['Identity']]);
            Assertion::isInstanceOf($entity, Street::class);
            Assertion::same($entity->getId(), (int) $row['Identity']);
            Assertion::same($entity->getName(), $row['Name']);
            Assertion::same($entity->getType(), $row['Type']);
            Assertion::same((string) $entity->getAdditionalName(), $row['Additional name']);
            Assertion::same($entity->getPlace()->getName(), $row['Place']);
        }
    }

    /**
     * @return EntityRepository<Province>
     */
    private function getProvinceRepository(): EntityRepository
    {
        return $this->getManager()->getRepository(Province::class);
    }

    /**
     * @return EntityRepository<District>
     */
    private function getDistrictRepository(): EntityRepository
    {
        return $this->getManager()->getRepository(District::class);
    }

    /**
     * @return EntityRepository<Community>
     */
    private function getCommunityRepository(): EntityRepository
    {
        return $this->getManager()->getRepository(Community::class);
    }

    /**
     * @return EntityRepository<PlaceType>
     */
    private function getPlaceTypeRepository(): EntityRepository
    {
        return $this->getManager()->getRepository(PlaceType::class);
    }

    /**
     * @return EntityRepository<CommunityType>
     */
    private function getCommunityTypeRepository(): EntityRepository
    {
        return $this->getManager()->getRepository(CommunityType::class);
    }

    /**
     * @return EntityRepository<Place>
     */
    private function getPlaceRepository(): EntityRepository
    {
        return $this->getManager()->getRepository(Place::class);
    }

    /**
     * @return EntityRepository<Street>
     */
    private function getStreetRepository(): EntityRepository
    {
        return $this->getManager()->getRepository(Street::class);
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
