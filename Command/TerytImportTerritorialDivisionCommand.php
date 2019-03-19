<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use FSi\Bundle\TerytDatabaseBundle\Teryt\Import\TerritorialDivisionNodeConverter;
use Symfony\Component\Console\Input\InputArgument;

class TerytImportTerritorialDivisionCommand extends TerytImportCommand
{
    const FLUSH_FREQUENCY = 1;

    protected function configure()
    {
        $this->setName('teryt:import:territorial-division')
            ->setDescription('Import teryt territorial division data from xml to database')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Territorial division xml file'
            );
    }

    /**
     * @param \SimpleXMLElement $node
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     * @return \FSi\Bundle\TerytDatabaseBundle\Teryt\Import\NodeConverter
     */
    public function getNodeConverter(\SimpleXMLElement $node, ObjectManager $om)
    {
        return new TerritorialDivisionNodeConverter($node, $om);
    }

    /**
     * @return string
     */
    protected function getRecordXPath()
    {
        return '/teryt/catalog/row';
    }
}
