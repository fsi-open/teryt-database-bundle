<?php

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Symfony\Component\Console\Input\InputArgument;

class TerytDownloadStreetsDatabaseCommand extends TerytDownloadCommand
{
    protected function configure()
    {
        $this->setName('teryt:download:streets')
            ->setDescription('Download teryt streets database files')
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Default target path where downloader will save teryt database file'
            )->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'Default streets file name',
                'streets'
            );
    }

    /**
     * @return string
     */
    protected function getFileDownloadUrl()
    {
        return $this->getTerytPageParser()
            ->getStreetsFileUrl($this->getContainer()->getParameter('fsi_teryt_db.files_list_page'));
    }

    /**
     * @return int
     */
    protected function getFileRoundedSize()
    {
        return $this->getTerytPageParser()
            ->getStreetsFileRoundedSize($this->getContainer()->getParameter('fsi_teryt_db.files_list_page'));
    }
}