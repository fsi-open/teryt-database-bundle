<?php

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Symfony\Component\Console\Input\InputArgument;

class TerytDownloadPlacesDatabaseCommand extends TerytDownloadCommand
{
    protected function configure()
    {
        $this->setName('teryt:download:places')
            ->setDescription('Download teryt places database files')
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Default target path where downloader will save teryt database file'
            )->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'Default places file name',
                'places'
            );
    }

    /**
     * @return string
     */
    protected function getFileDownloadUrl()
    {
        return $this->getTerytPageParser()
            ->getPlacesFileUrl($this->getContainer()->getParameter('fsi_teryt_db.files_list_page'));
    }

    /**
     * @return int
     */
    protected function getFileRoundedSize()
    {
        return $this->getTerytPageParser()
            ->getPlacesFileRoundedSize($this->getContainer()->getParameter('fsi_teryt_db.files_list_page'));
    }
}