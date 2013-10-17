<?php

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Symfony\Component\Console\Input\InputArgument;

class TerytDownloadPlacesDictionaryDatabaseCommand extends TerytDownloadCommand
{
    protected function configure()
    {
        $this->setName('teryt:download:places-dictionary')
            ->setDescription('Download teryt places dictionary database files')
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Default target path where downloader will save teryt database file'
            )->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'Default places dictionary file name',
                'places-dictionary'
            );
    }

    /**
     * @return string
     */
    protected function getFileDownloadUrl()
    {
        return $this->getTerytPageParser()
            ->getPlacesDictionaryFileUrl($this->getContainer()->getParameter('fsi_teryt_db.files_list_page'));
    }

    /**
     * @return int
     */
    protected function getFileRoundedSize()
    {
        return $this->getTerytPageParser()
            ->getPlacesDictionaryFileRoundedSize($this->getContainer()->getParameter('fsi_teryt_db.files_list_page'));
    }
}