<?php

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use FSi\Bundle\TerytDatabaseBundle\Behat\Context\Console\ApplicationTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;

class ImportTerytCommandContext extends BehatContext implements KernelAwareInterface
{
    private $parameters;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
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
     * @Given /^"([^"]*)" command was already called$/
     */
    public function commandWasAlreadyCalled($command)
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $tester = new ApplicationTester($application);
        $tester->run($command);
    }

    /**
     * @Given /^"([^"]*)" file have following content:$/
     */
    public function xmlFileHaveFollowingContent($fileName, PyStringNode $fileContent)
    {
        $targetFolder = sprintf("%s/Project/app/teryt", $this->parameters['fixtures_path']);
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
        expect($this->getPlaceDictionaryRepository()
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
        }
    }


    /**
     * @Then /^I should see "([^"]*)" console output$/
     */
    public function iShouldSeeConsoleOutput($output)
    {
        expect(trim($this->getMainContext()->getSubcontext('command')->getLastCommandOutput()))->toBe($output);
    }

    /**
     * @Then /^places dictionary table in database should have following records$/
     */
    public function placesDictionaryTableInDatabaseShouldHaveFollowingRecords(TableNode $table)
    {
        $tableHash = $table->getHash();

        foreach ($tableHash as $row) {
            $entity = $this->getPlaceDictionaryRepository()->findOneByType($row['Type']);
            expect($entity)->toBeAnInstanceOf('FSi\Bundle\TerytDatabaseBundle\Entity\PlaceDictionary');
            expect($entity->getName())->toBe($row['Name']);
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
    private function getPlaceDictionaryRepository()
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:PlaceDictionary');
    }
}