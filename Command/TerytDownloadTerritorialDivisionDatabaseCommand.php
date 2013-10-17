<?php

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Symfony\Component\Console\Input\InputArgument;

class TerytDownloadTerritorialDivisionDatabaseCommand extends TerytDownloadCommand
{
    protected function configure()
    {
        $this->setName('teryt:download:territorial-division')
            ->setDescription('Download teryt territorial division database files')
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Default target path where downloader will save teryt database file'
            )->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'Default territorial division file name',
                'territorial-division'
            );
    }

    /**
     * @return string
     */
    protected function getFileDownloadUrl()
    {
        return $this->getTerytPageParser()
            ->getTerritorialDivisionFileUrl($this->getContainer()->getParameter('fsi_teryt_db.files_list_page'));
    }

    /**
     * @return int
     */
    protected function getFileRoundedSize()
    {
        return $this->getTerytPageParser()
            ->getTerritorialDivisionFileRoundedSize($this->getContainer()->getParameter('fsi_teryt_db.files_list_page'));
    }
}