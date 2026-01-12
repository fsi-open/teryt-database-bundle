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
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use FSi\Bundle\TerytDatabaseBundle\Teryt\Import\NodeConverter;
use FSi\Bundle\TerytDatabaseBundle\Xml\Parser;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class TerytImportCommand extends Command
{
    public const FLUSH_FREQUENCY = 2000;

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /** @var resource */
    protected $handle;

    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * @var int
     */
    private $recordsCount = 0;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct();

        $this->managerRegistry = $managerRegistry;
    }

    abstract public function getNodeConverter(SimpleXMLElement $node, ObjectManager $om): NodeConverter;

    abstract protected function getRecordXPath(): string;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $xmlFile = $input->getArgument('file');
        Assertion::string($xmlFile);

        if (!file_exists($xmlFile)) {
            $output->writeln(sprintf('File %s does not exist', $xmlFile));
            return 1;
        }

        $xmlParser = $this->createXmlParser();

        $fileSize = filesize($xmlFile);
        Assertion::integer($fileSize);
        $this->progressBar = new ProgressBar($output, $fileSize);
        $this->progressBar->start();

        $this->importXmlFile($xmlParser, $xmlFile);

        $this->flushAndClear();
        $this->progressBar->finish();

        $output->writeln(sprintf("\nImported %d records.", $this->recordsCount));

        return 0;
    }

    private function createXmlParser(): Parser
    {
        $xmlParser = new Parser();

        return $xmlParser->registerCallback(
            $this->getRecordXPath(),
            $this->getNodeParserCallbackFunction()
        );
    }

    private function getNodeParserCallbackFunction(): callable
    {
        $counter = static::FLUSH_FREQUENCY;

        return function (Parser $parser, SimpleXMLElement $node) use (&$counter) {
            $this->convertNodeToPersistedEntity($node);
            $this->updateProgressHelper();

            $this->recordsCount++;
            $counter--;
            if ($counter === 0) {
                $counter = static::FLUSH_FREQUENCY;
                $this->flushAndClear();
            }
        };
    }

    private function convertNodeToPersistedEntity(SimpleXMLElement $node): void
    {
        $om = $this->getObjectManager();
        $om->persist($this->getNodeConverter($node, $om)->convertToEntity());
    }

    private function updateProgressHelper(): void
    {
        $pos = ftell($this->handle);
        Assertion::integer($pos);

        $this->progressBar->setProgress($pos);
    }

    private function flushAndClear(): void
    {
        $this->getObjectManager()->flush();
        $this->getObjectManager()->clear();
    }

    private function importXmlFile(Parser $xmlParser, string $xmlFile): void
    {
        $handle = fopen($xmlFile, 'rb');
        Assertion::isResource($handle);

        $this->handle = $handle;
        $xmlParser->parse($this->handle);
        fclose($this->handle);
    }

    private function getObjectManager(): ObjectManager
    {
        return $this->managerRegistry->getManager();
    }
}
