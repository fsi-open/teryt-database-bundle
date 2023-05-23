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
use Behat\Symfony2Extension\Context\KernelAwareContext;
use FSi\Bundle\TerytDatabaseBundle\Behat\Context\Console\ApplicationTester;
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

    public function __construct(string $fixturesPath)
    {
        $this->fixturesPath = $fixturesPath;
    }

    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }

    /**
     * @When /^I successfully run console command "([^"]*)"$/
     */
    public function iRunConsoleCommand(string $command): void
    {
        $application = new Application($this->kernel);
        $tester = new ApplicationTester($application);

        Assertion::eq($tester->run([$command]), 0);
        $this->lastCommandOutput = $tester->getDisplay(true);
    }

    /**
     * @When /^I run console command "([^"]*)" with argument "--([^"]*)=([^"]*)"$/
     */
    public function iRunConsoleCommandWithArgument(string $command, string $argument, string $value = ''): void
    {
        $application = new Application($this->kernel);
        $tester = new ApplicationTester($application);

        $value = $this->prepareValue($argument, $value);

        $this->lastCommandExitCode = $tester->run(
            [
            $command,
            $argument => $value
            ]
        );

        $this->lastCommandOutput = $tester->getDisplay(true);
    }

    /**
     * @When /^I unsuccessfully run console command "([^"]*)" with argument "--([^"]*)=([^"]*)"$/
     */
    public function iUnsuccessfullyRunConsoleCommandWithArgument(string $command, string $argument, string $value): void
    {
        $application = new Application($this->kernel);
        $tester = new ApplicationTester($application);

        Assertion::eq($tester->run([
            $command,
            $argument => $value
        ]), 1);

        $this->lastCommandOutput = $tester->getDisplay(true);
    }


    /**
     * @When /^I successfully run console command "([^"]*)" with argument "--([^"]*)=([^"]*)"$/
     */
    public function iSuccessfullyRunConsoleCommandWithArgument(string $command, string $argument, string $value): void
    {
        $application = new Application($this->kernel);
        $tester = new ApplicationTester($application);

        $value = $this->prepareValue($argument, $value);

        Assertion::eq($tester->run([
            $command,
            $argument => $value
        ]), 0);

        $this->lastCommandOutput = $tester->getDisplay(true);
    }

    /**
     * @Then /^I should see "([^"]*)" console output$/
     * @Given /^I should see "([^"]*)" output at console$/
     */
    public function iShouldSeeOutputAtConsole(string $consoleOutput): void
    {
        Assertion::eq(trim($this->getLastCommandOutput()), $consoleOutput);
    }

    public function getLastCommandOutput(): string
    {
        return $this->lastCommandOutput;
    }

    public function prepareValue(string $argument, string $value): string
    {
        if ($argument === 'file') {
            return $this->kernel->getRootDir() . DIRECTORY_SEPARATOR . $value;
        }

        return $value;
    }
}
