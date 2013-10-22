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
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class TerytImportCommand extends ContainerAwareCommand
{
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
        $xmlParser->parse(fopen($xmlFile, 'r'));

        return 0;
    }

    /**
     * @return callable
     */
    protected function getNodeParserCallbackFunction()
    {
        $om = $this->getContainer()->get('doctrine')->getManager();
        $self = $this;

        return function (Parser $parser, \SimpleXMLElement $node) use ($om, $self) {
            $converter = $self->getNodeConverter($node, $om);
            $entity = $converter->convertToEntity();
            $om->persist($entity);
            $om->flush();
            $om->clear();
        };
    }
}