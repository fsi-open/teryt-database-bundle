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

    private $terytFixturesPath;

    private $lastCommandOutput;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(array $parameters)
    {
        $this->terytFixturesPath = __DIR__ . '/../Fixtures/TerytPage';
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
     * @When /^I run console command "([^"]*)"$/
     */
    public function iRunConsoleCommand($command)
    {
        $this->prepareGuzzleResponses($command);
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $tester = new ApplicationTester($application);

        $tester->run($command);
        $this->lastCommandOutput = $tester->getDisplay(true);
    }

    /**
     * @Then /^"([^"]*)" file should be downloaded into "([^"]*)" folder$/
     */
    public function fileShouldBeDownloadedIntoFolder($fileName, $targetFilesPath)
    {
        $downloadPath = realpath( __DIR__ . '/../' . $targetFilesPath);
        $filePath = $downloadPath . '/' . $fileName;
        expect(file_exists($filePath))->toBe(true);
        unlink($filePath);
        rmdir($downloadPath);
    }

    /**
     * @Given /^I should see "([^"]*)" output at console$/
     */
    public function iShouldSeeOutputAtConsole($consoleOutput)
    {
        expect(trim($this->lastCommandOutput))->toBe($consoleOutput);
    }

    private function prepareGuzzleResponses($command)
    {
        $mock = $this->createGuzzleMockPlugin();

        $fileUrlResponse = new \Guzzle\Http\Message\Response(200);
        switch ($command) {
            case 'teryt:download:streets':
                $fileUrlResponse->setBody(file_get_contents($this->terytFixturesPath . DIRECTORY_SEPARATOR . 'streets.zip'));
            case 'teryt:download:places':
                $fileUrlResponse->setBody(file_get_contents($this->terytFixturesPath . DIRECTORY_SEPARATOR . 'places.zip'));
                break;
            case 'teryt:download:places-dictionary':
                $fileUrlResponse->setBody(file_get_contents($this->terytFixturesPath . DIRECTORY_SEPARATOR . 'places-dictionary.zip'));
                break;
            case 'teryt:download:territorial-division':
                $fileUrlResponse->setBody(file_get_contents($this->terytFixturesPath . DIRECTORY_SEPARATOR . 'territorial-division.zip'));
                break;
            default:
                throw new BehaviorException(sprintf("Unknown command \"%s\"", $command));
                break;
        }

        $mock->addResponse($fileUrlResponse);
        $this->kernel->getContainer()->get('fsi_teryt_db.http_client')->addSubscriber($mock);
    }

    /**
     * @return MockPlugin
     */
    private function createGuzzleMockPlugin()
    {
        $mock = new MockPlugin();
        $downloadPageResponse = new \Guzzle\Http\Message\Response(200);
        $downloadPageResponse->setBody(file_get_contents($this->terytFixturesPath . DIRECTORY_SEPARATOR . 'listTerytFiles.html'));
        $mock->addResponse($downloadPageResponse);

        return $mock;
    }
}