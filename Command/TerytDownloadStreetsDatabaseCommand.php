<?php

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use FSi\Bundle\TerytDatabaseBundle\Teryt\DownloadPageParser;
use Guzzle\Http\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TerytDownloadStreetsDatabaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('teryt:download:streets')
            ->setDescription('Download teryt database files')
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Default target path where downloader will save teryt database files'
            )->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'Default streets file name',
                'streets'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $target = $input->getArgument('target');
        if (!isset($target)) {
            $target = $this->getDefaultTargetPath();
        }

        if (!file_exists($target)) {
            mkdir($target);
        }

        $client = $this->getContainer()->get('fsi_teryt_db.http_client');
        $terytPageParser = new DownloadPageParser($client);

        $streetFilesUrl = $terytPageParser->getPlacesDictionaryFileUrl($this->getContainer()->getParameter('fsi_teryt_db.files_list_page'));

        $client->get($streetFilesUrl, null, array(
            'connect_timeout' => 10,
            'save_to' => sprintf('%s/%s.zip', $target, $input->getArgument('filename'))
        ))->send();

        return 0;
    }

    private function getDefaultTargetPath()
    {
        return $this->getContainer()->getParameter('kernel.root_dir') . '/teryt';
    }
}