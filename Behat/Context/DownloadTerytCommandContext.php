<?php

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;

class DownloadTerytCommandContext implements KernelAwareContext
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

    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^Urls to Teryt database files are available at "([^"]*)"$/
     */
    public function urlsToTerytDatabaseFilesAreAvailableAt($terytFilesPageAdr)
    {
        expect($this->kernel->getContainer()->getParameter('fsi_teryt_db.files_list_page'))->toBe($terytFilesPageAdr);
    }

    /**
     * @Then /^"([^"]*)" file should be downloaded into "([^"]*)" folder$/
     */
    public function fileShouldBeDownloadedIntoFolder($fileName, $targetFilesPath)
    {
        $downloadPath = realpath( __DIR__ . '/../' . $targetFilesPath);
        $filePath = $downloadPath . '/' . $fileName;
        expect(file_exists($filePath))->toBe(true);
    }

    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
        $terytDownloadPath = $this->fixturesPath . '/Project/app/teryt';
        if (!file_exists($terytDownloadPath)) {
            return;
        }

        foreach (new \DirectoryIterator($terytDownloadPath) as $file) {
            if ($file->isDot()) {
                continue;
            }
            unlink($terytDownloadPath . DIRECTORY_SEPARATOR . $file->getFilename());
        }

        rmdir($terytDownloadPath);
    }
}