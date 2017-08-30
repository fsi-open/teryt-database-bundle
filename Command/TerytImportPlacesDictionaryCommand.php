<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use FSi\Bundle\TerytDatabaseBundle\Teryt\Import\PlacesDictionaryNodeConverter;
use Symfony\Component\Console\Input\InputArgument;

class TerytImportPlacesDictionaryCommand extends TerytImportCommand
{
    protected function configure()
    {
        $this->setName('teryt:import:places-dictionary')
            ->setDescription('Import places dictionary data from xml to database')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Places dictionary xml file'
            );
    }

    /**
     * @param \SimpleXMLElement $node
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     * @return \FSi\Bundle\TerytDatabaseBundle\Teryt\Import\NodeConverter
     */
    public function getNodeConverter(\SimpleXMLElement $node, ObjectManager $om)
    {
        return new PlacesDictionaryNodeConverter($node, $om);
    }

    /**
     * @return string
     */
    protected function getRecordXPath()
    {
        return '/simc/catalog/row';
    }
}
