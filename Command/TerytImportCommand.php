<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Hobnob\XmlStreamReader\Parser;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class TerytImportCommand extends ContainerAwareCommand
{
    const FLUSH_FREQUENCY = 2000;

    /** @var resource */
    protected $handle;

    /**
     * @param \SimpleXMLElement $node
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     * @return \FSi\Bundle\TerytDatabaseBundle\Teryt\Import\NodeConverter
     */
    abstract public function getNodeConverter(\SimpleXMLElement $node, ObjectManager $om);

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $xmlFile = $input->getArgument('file');

        if (!file_exists($xmlFile)) {
            $output->writeln(sprintf('File %s does not exist', $xmlFile));
            return 1;
        }

        $xmlParser = new Parser();
        $xmlParser->registerCallback(
            '/teryt/catalog/row',
            $this->getNodeParserCallbackFunction()
        );

        $this->handle = fopen($xmlFile, 'r');
        /** @var ProgressHelper $progress */
        $this->getProgressHelper()->start($output, filesize($xmlFile));
        $xmlParser->parse($this->handle);
        $this->flushAndClear();
        fclose($this->handle);
        $this->getProgressHelper()->finish();

        return 0;
    }

    /**
     * @return callable
     */
    protected function getNodeParserCallbackFunction()
    {
        $om = $this->getEntityManager();
        $counter = self::FLUSH_FREQUENCY;

        return function (Parser $parser, \SimpleXMLElement $node) use ($om, &$counter) {
            $converter = $this->getNodeConverter($node, $om);
            $entity = $converter->convertToEntity();
            $om->persist($entity);

            $this->getProgressHelper()->setCurrent(ftell($this->handle), true);

            $counter--;
            if (!$counter) {
                $counter = self::FLUSH_FREQUENCY;
                $this->flushAndClear();
            }
        };
    }

    private function flushAndClear()
    {
        $om = $this->getEntityManager();
        $om->flush();
        $om->clear();
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @return ProgressHelper
     */
    private function getProgressHelper()
    {
        return $this->getHelperSet()->get('progress');
    }
}