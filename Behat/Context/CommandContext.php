<?php

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\BehaviorException;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use FSi\Bundle\TerytDatabaseBundle\Behat\Context\Console\ApplicationTester;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;

class CommandContext extends BehatContext implements KernelAwareInterface
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
     * @var int
     */
    protected $lastCommandExitCode;

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
     * @When /^I successfully run console command "([^"]*)"$/
     */
    public function iRunConsoleCommand($command)
    {
        $this->prepareCommandEnv($command);

        $application = new Application($this->kernel);
        $tester = new ApplicationTester($application);

        expect($tester->run($command))->toBe(0);
        $this->lastCommandOutput = $tester->getDisplay(true);
    }

    /**
     * @When /^I run console command "([^"]*)" with arguments "--([^"]*)=([^"]*)"$/
     */
    public function iRunConsoleCommandWithArguments($command, $argument, $value)
    {
        $application = new Application($this->kernel);
        $tester = new ApplicationTester($application);

        $value = $this->prepareValue($argument, $value);

        $this->lastCommandExitCode = $tester->run(array(
            $command,
            $argument => $value
        ));

        $this->lastCommandOutput = $tester->getDisplay(true);
    }

    /**
     * @When /^I unsuccessfully run console command "([^"]*)" with arguments "--([^"]*)=([^"]*)"$/
     */
    public function iUnsuccessfullyRunConsoleCommandWithArguments($command, $argument, $value)
    {
        $application = new Application($this->kernel);
        $tester = new ApplicationTester($application);

        expect($tester->run(array(
            $command,
            $argument => $value
        )))->toBe(1);

        $this->lastCommandOutput = $tester->getDisplay(true);
    }


    /**
     * @When /^I successfully run console command "([^"]*)" with arguments "--([^"]*)=([^"]*)"$/
     */
    public function iSuccessfullyRunConsoleCommandWithArguments($command, $argument, $value)
    {
        $application = new Application($this->kernel);
        $tester = new ApplicationTester($application);

        $value = $this->prepareValue($argument, $value);

        expect($tester->run(array(
            $command,
            $argument => $value
        )))->toBe(0);

        $this->lastCommandOutput = $tester->getDisplay(true);
    }

    /**
     * @return mixed
     */
    public function getLastCommandOutput()
    {
        return $this->lastCommandOutput;
    }

    /**
     * @param $command
     */
    private function prepareCommandEnv($command)
    {
        if (strpos($command, 'teryt:download') !== false) {
            $this->prepareHttpResponses($command);
        }
    }

    private function prepareHttpResponses($command)
    {
        $mock = $this->createGuzzleMockPlugin();

        $fileUrlResponse = new Response(200);
        $terytPageFixturesPath = $this->parameters['fixtures_path'] . '/TerytPage';

        switch ($command) {
            case 'teryt:download:streets':
                $fileUrlResponse->setBody(file_get_contents($terytPageFixturesPath . DIRECTORY_SEPARATOR . 'streets.zip'));
            case 'teryt:download:places':
                $fileUrlResponse->setBody(file_get_contents($terytPageFixturesPath . DIRECTORY_SEPARATOR . 'places.zip'));
                break;
            case 'teryt:download:places-dictionary':
                $fileUrlResponse->setBody(file_get_contents($terytPageFixturesPath . DIRECTORY_SEPARATOR . 'places-dictionary.zip'));
                break;
            case 'teryt:download:territorial-division':
                $fileUrlResponse->setBody(file_get_contents($terytPageFixturesPath . DIRECTORY_SEPARATOR . 'territorial-division.zip'));
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
        $terytPageFixturesPath = $this->parameters['fixtures_path'] . '/TerytPage';
        $mock = new MockPlugin();
        $downloadPageResponse = new Response(200);
        $downloadPageResponse->setBody(file_get_contents($terytPageFixturesPath . DIRECTORY_SEPARATOR . 'listTerytFiles.html'));
        $mock->addResponse($downloadPageResponse);

        return $mock;
    }

    /**
     * @param $argument
     * @param $value
     * @return string
     */
    public function prepareValue($argument, $value)
    {
        switch ($argument) {
            case 'file':
                $value = $this->kernel->getRootDir() . DIRECTORY_SEPARATOR . $value;
                break;
        }

        return $value;
    }
}