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
use FSi\Bundle\TerytDatabaseBundle\Teryt\Import\StreetsNodeConverter;
use SimpleXMLElement;
use Symfony\Component\Console\Input\InputArgument;

class TerytImportStreetsCommand extends TerytImportCommand
{
    protected function configure(): void
    {
        $this->setName('teryt:import:streets')
            ->setDescription('Import streets data from xml to database')
            ->addArgument('file', InputArgument::REQUIRED, 'Places streets xml file');
    }

    public function getNodeConverter(SimpleXMLElement $node, ObjectManager $om): NodeConverter
    {
        return new StreetsNodeConverter($node, $om);
    }

    protected function getRecordXPath(): string
    {
        return '/ulic/catalog/row';
    }
}
