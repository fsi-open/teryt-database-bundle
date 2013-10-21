<?php

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use FSi\Bundle\TerytDatabaseBundle\Entity\Community;
use FSi\Bundle\TerytDatabaseBundle\Entity\District;
use FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType;
use FSi\Bundle\TerytDatabaseBundle\Entity\Province;
use Symfony\Component\HttpKernel\KernelInterface;


class DataContext extends BehatContext implements KernelAwareInterface
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
     * @var array
     */
    protected $parameters;

    function __construct($parameters = array())
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
     * @Given /^there is a community in database with code "([^"]*)" and name "([^"]*)"$/
     */
    public function thereIsACommunityInDatabaseWithCodeAndName($code, $name)
    {
        $community = new Community();
        $community->setCode($code)
            ->setName($name);

        $this->kernel->getContainer()->get('doctrine')->getManager()->persist($community);
        $this->kernel->getContainer()->get('doctrine')->getManager()->flush();
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

    protected function findProvinceByName($name)
    {
        return $this->kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('FSiTerytDbBundle:Province')
            ->findOneByName($name);
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
}