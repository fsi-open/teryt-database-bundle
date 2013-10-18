<?php

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\BehaviorException;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Buzz\Client\Curl;
use Buzz\Client\FileGetContents;
use Buzz\Message\Request;
use Buzz\Message\Response;
use FSi\Bundle\TerytDatabaseBundle\Behat\Context\Console\ApplicationTester;
use Guzzle\Plugin\Mock\MockPlugin;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;

class DownloadTerytCommandContext extends BehatContext implements KernelAwareInterface
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
     * @Given /^I should see "([^"]*)" output at console$/
     */
    public function iShouldSeeOutputAtConsole($consoleOutput)
    {
        expect(trim($this->getMainContext()->getSubcontext('command')->getLastCommandOutput()))->toBe($consoleOutput);
    }

    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
        $terytDownloadPath = $this->parameters['fixtures_path'] . '/Project/app/teryt';
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