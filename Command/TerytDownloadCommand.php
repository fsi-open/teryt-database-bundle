<?php

namespace FSi\Bundle\TerytDatabaseBundle\Command;


use FSi\Bundle\TerytDatabaseBundle\Teryt\DownloadPageParser;
use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class TerytDownloadCommand extends ContainerAwareCommand
{
    /**
     * @return string
     */
    abstract protected function getFileDownloadUrl();

    /**
     * @return int
     */
    abstract protected function getFileRoundedSize();

    /**
     * @return DownloadPageParser
     */
    protected function getTerytPageParser()
    {
        if (!isset($this->terytPageParser)) {
            $this->terytPageParser = new DownloadPageParser($this->getContainer()->get('fsi_teryt_db.http_client'));
        }

        return $this->terytPageParser;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $target = $this->getDownloadTargetFolder($input);
        $request = $this->createDownloadHttpRequest($input, $target);

        $progressHelper = $this->getHelperSet()->get('progress');
        $progressHelper->start($output, 100);

        $request->getEventDispatcher()->addListener(
            'curl.callback.progress',
            $this->getDownloadProgressCallbackFunction($output, $progressHelper)
        );

        $request->send();

        $progressHelper->setCurrent(100, true);
        $progressHelper->finish();

        $output->writeln("");

        return 0;
    }

    protected function getDefaultTargetPath()
    {
        return $this->getContainer()->getParameter('kernel.root_dir') . '/teryt';
    }

    /**
     * @param InputInterface $input
     * @return mixed|string
     */
    private function getDownloadTargetFolder(InputInterface $input)
    {
        $target = $input->getArgument('target');
        if (!isset($target)) {
            $target = $this->getDefaultTargetPath();
        }

        if (!file_exists($target)) {
            mkdir($target);
            return $target;
        }

        return $target;
    }

    /**
     * @param InputInterface $input
     * @param $target
     * @return mixed
     */
    protected function createDownloadHttpRequest(InputInterface $input, $target)
    {
        $client = $this->getContainer()->get('fsi_teryt_db.http_client');

        $request = $client->get($this->getFileDownloadUrl(), null, array(
            'connect_timeout' => 10,
            'save_to' => sprintf('%s/%s.zip', $target, $input->getArgument('filename')),
        ));

        $request->getCurlOptions()->set('progress', true);

        return $request;
    }

    /**
     * @param OutputInterface $output
     * @param $progressHelper
     * @return callable
     */
    protected function getDownloadProgressCallbackFunction(OutputInterface $output, $progressHelper)
    {
        $fileSize = $this->getFileRoundedSize();

        return function (Event $event) use ($output, $fileSize, $progressHelper) {
            if ($event['downloaded'] === 0) {
                return;
            }

            $percent = ($event['downloaded'] / $fileSize) * 100;

            if ($percent > 100) {
                return;
            }

            $progressHelper->setCurrent((int)$percent, true);
        };
    }
}