<?php

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context\Console;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
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

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param array $input
     *
     * @return integer
     */
    public function run($input = array())
    {
        $this->input = new ArrayInput((array) $input);
        $this->output = new StreamOutput(fopen('php://memory', 'r+', false));

        $inputStream = $this->getInputStream();
        rewind($inputStream);
        $this->application->getHelperSet()
            ->get('dialog')
            ->setInputStream($inputStream);

        return $this->application->doRun($this->input, $this->output);
    }

    /**
     * @param boolean
     *
     * @return string
     */
    public function getDisplay($normalize = false)
    {
        rewind($this->output->getStream());

        $display = stream_get_contents($this->output->getStream());

        if ($normalize) {
            $display = str_replace(PHP_EOL, "\n", $display);
        }

        return $display;
    }

    /**
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $input
     */
    public function putToInputStream($input)
    {
        fputs($this->getInputStream(), $input);
    }

    /**
     * @return resource
     */
    private function getInputStream()
    {
        if (null === $this->inputStream) {
            $this->inputStream = fopen('php://memory', 'r+', false);
        }

        return $this->inputStream;
    }
}