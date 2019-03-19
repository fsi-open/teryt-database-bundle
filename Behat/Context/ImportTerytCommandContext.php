<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\ORM\EntityRepository;
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

    public function __construct($fixturesPath)
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
        $targetFolder = sprintf('%s/Project/app/teryt', $this->fixturesPath);
        if (!file_exists($targetFolder) && !mkdir($targetFolder) && !is_dir($targetFolder)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $targetFolder));
        }

        $filePath = sprintf('%s/%s', $targetFolder, $fileName);
        file_put_contents($filePath, $fileContent->getRaw());
        expect(file_exists($filePath))->toBe(true);
    }

    /**
     * @Given /^there are no provinces in database$/
     */
    public function thereAreNoProvincesInDatabase(): void
    {
        expect($this->getProvinceRepository()->findAll())->toBe([]);
    }

    /**
     * @Given /^there are no districts in database$/
     */
    public function thereAreNoDistrictsInDatabase(): void
    {
        expect($this->getDistrictRepository()->findAll())->toBe([]);
    }

    /**
     * @Given /^there are no communities in database$/
     */
    public function thereAreNoCommunitiesInDatabase(): void
    {
        expect($this->getCommunityRepository()->findAll())->toBe([]);
    }

    /**
     * @Given /^places dictionary table in database is empty$/
     */
    public function placesDictionaryTableInDatabaseIsEmpty(): void
    {
        expect($this->getPlaceTypeRepository()->findAll())->toBe([]);
    }

    /**
     * @Given /^places table in database is empty$/
     */
    public function placesTableInDatabaseIsEmpty(): void
    {
        expect($this->getPlaceRepository()->findAll())->toBe([]);
    }

    /**
     * @Given /^there are no streets in database$/
     */
    public function thereAreNoStreetsInDatabase(): void
    {
        expect($this->getStreetRepository()
            ->findAll())->toBe([]);
    }

    /**
     * @Then /^following province should exist in database$/
     */
    public function followingProvinceShouldExistInDatabase(TableNode $table): void
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            /** @var Province|null $entity */
            $entity = $this->getProvinceRepository()->findOneByCode($row['Code']);
            expect($entity)->toBeAnInstanceOf(Province::class);
            expect($entity->getName())->toBe($row['Name']);
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
            $entity = $this->getDistrictRepository()->findOneByCode($row['Code']);
            expect($entity)->toBeAnInstanceOf(District::class);
            expect($entity->getName())->toBe($row['Name']);
            expect($entity->getProvince()->getName())->toBe($row['Province']);
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
            $entity = $this->getCommunityRepository()->findOneByCode($row['Code']);
            expect($entity)->toBeAnInstanceOf(Community::class);
            expect($entity->getName())->toBe($row['Name']);
            expect($entity->getDistrict()->getName())->toBe($row['District']);
            expect($entity->getType()->getName())->toBe($row['Community type']);
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
            $entity = $this->getCommunityTypeRepository()->findOneByType($row['Type']);
            expect($entity)->toBeAnInstanceOf(CommunityType::class);
            expect($entity->getName())->toBe($row['Name']);
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
            $entity = $this->getPlaceTypeRepository()->findOneByType($row['Type']);
            expect($entity)->toBeAnInstanceOf(PlaceType::class);
            expect($entity->getName())->toBe($row['Name']);
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
            $entity = $this->getPlaceRepository()->findOneById($row['Identity']);
            expect($entity)->toBeAnInstanceOf(Place::class);
            expect($entity->getName())->toBe($row['Name']);
            expect($entity->getType()->getName())->toBe($row['Place type']);
            expect($entity->getCommunity()->getName())->toBe($row['Community']);
            if (!empty($row['Parent place'])) {
                expect($entity->getParentPlace()->getName())->toBe($row['Parent place']);
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
            $entity = $this->getStreetRepository()->findOneById($row['Identity']);
            expect($entity)->toBeAnInstanceOf(Street::class);
            expect($entity->getId())->toBe((int) $row['Identity']);
            expect($entity->getName())->toBe($row['Name']);
            expect($entity->getType())->toBe($row['Type']);
            expect((string) $entity->getAdditionalName())->toBe($row['Additional name']);
            expect($entity->getPlace()->getName())->toBe($row['Place']);
        }
    }

    private function getProvinceRepository(): EntityRepository
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(Province::class);
    }

    private function getDistrictRepository(): EntityRepository
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(District::class);
    }

    private function getCommunityRepository(): EntityRepository
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(Community::class);
    }

    private function getPlaceTypeRepository(): EntityRepository
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(PlaceType::class);
    }

    private function getCommunityTypeRepository(): EntityRepository
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(CommunityType::class);
    }

    private function getPlaceRepository(): EntityRepository
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(Place::class);
    }

    private function getStreetRepository(): EntityRepository
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(Street::class);
    }
}
