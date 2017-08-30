<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Hobnob\XmlStreamReader\Parser;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class TerytImportCommand extends ContainerAwareCommand
{
    const FLUSH_FREQUENCY = 2000;

    /** @var resource */
    protected $handle;

    /**
     * @var ProgressBar
     */
    private $progressBar;

    private $recordsCount = 0;

    /**
     * @param SimpleXMLElement $node
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     * @return \FSi\Bundle\TerytDatabaseBundle\Teryt\Import\NodeConverter
     */
    abstract public function getNodeConverter(SimpleXMLElement $node, ObjectManager $om);

    /**
     * @return string
     */
    abstract protected function getRecordXPath();

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $xmlFile = $input->getArgument('file');

        if (!file_exists($xmlFile)) {
            $output->writeln(sprintf('File %s does not exist', $xmlFile));
            return 1;
        }

        $xmlParser = $this->createXmlParser();

        $this->progressBar = new ProgressBar($output, filesize($xmlFile));
        $this->progressBar->start();

        $this->importXmlFile($xmlParser, $xmlFile);

        $this->flushAndClear();
        $this->progressBar->finish();

        $output->writeln(sprintf("\nImported %d records.", $this->recordsCount));

        return 0;
    }

    /**
     * @return Parser
     * @throws \Exception
     */
    private function createXmlParser()
    {
        $xmlParser = new Parser();

        return $xmlParser->registerCallback(
            $this->getRecordXPath(),
            $this->getNodeParserCallbackFunction()
        );
    }

    /**
     * @return callable
     */
    private function getNodeParserCallbackFunction()
    {
        $counter = static::FLUSH_FREQUENCY;

        return function (Parser $parser, SimpleXMLElement $node) use (&$counter) {
            $this->convertNodeToPersistedEntity($node);
            $this->updateProgressHelper();

            $this->recordsCount++;
            $counter--;
            if (!$counter) {
                $counter = static::FLUSH_FREQUENCY;
                $this->flushAndClear();
            }
        };
    }

    /**
     * @param SimpleXMLElement $node
     */
    private function convertNodeToPersistedEntity(SimpleXMLElement $node)
    {
        $om = $this->getObjectManager();
        $converter = $this->getNodeConverter($node, $om);
        $om->persist(
            $converter->convertToEntity()
        );
    }

    private function updateProgressHelper()
    {
        $this->progressBar->setProgress(ftell($this->handle));
    }

    private function flushAndClear()
    {
        $this->getObjectManager()->flush();
        $this->getObjectManager()->clear();
    }

    /**
     * @param Parser $xmlParser
     * @param string $xmlFile
     */
    private function importXmlFile(Parser $xmlParser, $xmlFile)
    {
        $this->handle = fopen($xmlFile, 'r');
        $xmlParser->parse($this->handle);
        fclose($this->handle);
    }

    /**
     * @return ObjectManager
     */
    private function getObjectManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }
}
