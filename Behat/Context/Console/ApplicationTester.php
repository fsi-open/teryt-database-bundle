<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context\Console;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class ApplicationTester
{
    /**
     * @var Application $application
     */
    private $application;

    /**
     * @var StringInput $input
     */
    private $input;

    /**
     * @var StreamOutput $output
     */
    private $output;

    /**
     * @var resource $inputStream
     */
    private $inputStream;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function run(array $input = []): int
    {
        $this->input = new ArrayInput($input);
        $this->input->setInteractive(false);

        $this->output = new StreamOutput(fopen('php://memory', 'r+', false));

        $this->initializeInputStream();
        rewind($this->inputStream);

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->application->getHelperSet()->get('question');
        $questionHelper->setInputStream($this->inputStream);

        return $this->application->doRun($this->input, $this->output);
    }

    public function getDisplay(bool $normalize = false): string
    {
        rewind($this->output->getStream());

        $display = stream_get_contents($this->output->getStream());

        if ($normalize) {
            $display = str_replace(PHP_EOL, "\n", $display);
        }

        return $display;
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function putToInputStream(string $input): void
    {
        $this->initializeInputStream();

        fwrite($this->inputStream, $input);
    }

    private function initializeInputStream(): void
    {
        if (null === $this->inputStream) {
            $this->inputStream = fopen('php://memory', 'r+', false);
        }
    }
}
