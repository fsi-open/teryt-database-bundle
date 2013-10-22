<?php

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use FSi\Bundle\TerytDatabaseBundle\Teryt\Import\PlacesDictionaryNodeConverter;
use FSi\Bundle\TerytDatabaseBundle\Teryt\Import\PlacesNodeConverter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TerytImportPlacesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('teryt:import:places')
            ->setDescription('Import places data from xml to database')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Places dictionary xml file'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $xmlFile = $input->getArgument('file');

        if (!file_exists($xmlFile)) {
            $output->writeln(sprintf('File %s does not exist', $xmlFile));
            return 1;
        }

        $xmlParser = new \Hobnob\XmlStreamReader\Parser();

        $objectManager = $this->getContainer()->get('doctrine')->getManager();
        $xmlParser->registerCallback(
            '/teryt/catalog/row',
            function(\Hobnob\XmlStreamReader\Parser $parser, \SimpleXMLElement $node) use ($objectManager) {
                $converter = new PlacesNodeConverter($node, $objectManager);
                $entity = $converter->convertToEntity();
                $objectManager->persist($entity);
                $objectManager->flush();
                $objectManager->clear();
            }
        );

        $xmlParser->parse(fopen($xmlFile, 'r'));

        return 0;
    }
}