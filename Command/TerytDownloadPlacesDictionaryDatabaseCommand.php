<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TerytDownloadPlacesDictionaryDatabaseCommand extends TerytDownloadCommand
{
    protected function configure(): void
    {
        $this->setName('teryt:download:places-dictionary')
            ->setDescription('Download teryt places dictionary (WMRODZ) database files')
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Default target path where downloader will save teryt database file'
            )
            ->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'Default target file where downloader will save teryt database file',
                'places-dictionary.zip'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->saveFile(
            $this->getApiClient()->getPlacesDictionaryData(),
            $input->getArgument('target') ?? $this->getDefaultTargetPath(),
            $input->getArgument('filename')
        );

        return 0;
    }
}
