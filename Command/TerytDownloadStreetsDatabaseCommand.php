<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Assert\Assertion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TerytDownloadStreetsDatabaseCommand extends TerytDownloadCommand
{
    protected function configure(): void
    {
        $this->setName('teryt:download:streets')
            ->setDescription('Download teryt streets database files')
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Default target path where downloader will save teryt database file'
            )
            ->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'Default target file where downloader will save teryt database file',
                'streets.zip'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $target = $input->getArgument('target');
        Assertion::nullOrString($target);

        $fileName = $input->getArgument('filename');
        Assertion::string($fileName);

        $this->saveFile($this->getApiClient()->getStreetsData(), $target ?? $this->getDefaultTargetPath(), $fileName);

        return 0;
    }
}
