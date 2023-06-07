<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Command;

use Doctrine\Persistence\ObjectManager;
use FSi\Bundle\TerytDatabaseBundle\Teryt\Import\NodeConverter;
use FSi\Bundle\TerytDatabaseBundle\Teryt\Import\TerritorialDivisionNodeConverter;
use SimpleXMLElement;
use Symfony\Component\Console\Input\InputArgument;

class TerytImportTerritorialDivisionCommand extends TerytImportCommand
{
    public const FLUSH_FREQUENCY = 1;

    protected function configure(): void
    {
        $this->setName('teryt:import:territorial-division')
            ->setDescription('Import teryt territorial division data from xml to database')
            ->addArgument('file', InputArgument::REQUIRED, 'Territorial division xml file');
    }

    public function getNodeConverter(SimpleXMLElement $node, ObjectManager $om): NodeConverter
    {
        return new TerritorialDivisionNodeConverter($node, $om);
    }

    protected function getRecordXPath(): string
    {
        return '/teryt/catalog/row';
    }
}
