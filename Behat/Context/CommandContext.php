<?php

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Symfony2Extension\Context\KernelAwareContext;
use FSi\Bundle\TerytDatabaseBundle\Behat\Context\Console\ApplicationTester;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;

class CommandContext implements KernelAwareContext
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
     * @var string
     */
    protected $fixturesPath;

    public function __construct($fixturesPath)
    {
        $this->fixturesPath = $fixturesPath;
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
        $application = new Application($this->kernel);
        $tester = new ApplicationTester($application);

        expect($tester->run($command))->toBe(0);
        $this->lastCommandOutput = $tester->getDisplay(true);
    }

    /**
     * @When /^I run console command "([^"]*)" with argument "--([^"]*)=([^"]*)"$/
     */
    public function iRunConsoleCommandWithArgument($command, $argument, $value = 1)
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
     * @When /^I unsuccessfully run console command "([^"]*)" with argument "--([^"]*)=([^"]*)"$/
     */
    public function iUnsuccessfullyRunConsoleCommandWithArgument($command, $argument, $value)
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
     * @When /^I successfully run console command "([^"]*)" with argument "--([^"]*)=([^"]*)"$/
     */
    public function iSuccessfullyRunConsoleCommandWithArgument($command, $argument, $value)
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
     * @Then /^I should see "([^"]*)" console output$/
     * @Given /^I should see "([^"]*)" output at console$/
     */
    public function iShouldSeeOutputAtConsole($consoleOutput)
    {
        expect(trim($this->getLastCommandOutput()))->toBe($consoleOutput);
    }

    /**
     * @return mixed
     */
    public function getLastCommandOutput()
    {
        return $this->lastCommandOutput;
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
