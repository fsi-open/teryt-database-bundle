<?php

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\Filesystem\Filesystem;
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

    public function __construct($fixturesPath)
    {
        $this->fixturesPath = $fixturesPath;
    }

    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
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

        $fileSystem = new Filesystem();
        $fileSystem->remove($terytDownloadPath);
    }
}
