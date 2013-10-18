<?php

namespace FSi\Bundle\TerytDatabaseBundle\Behat\Context;

use Behat\Behat\Context\BehatContext;

class FeatureContext extends BehatContext
{
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->useContext('data', new DataContext($parameters));
        $this->useContext('command', new CommandContext($parameters));
        $this->useContext('download-command', new DownloadTerytCommandContext($parameters));
        $this->useContext('import-command', new ImportTerytCommandContext($parameters));
    }
}