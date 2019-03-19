<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use Doctrine\Common\Persistence\ObjectManager;
use SimpleXMLElement;

abstract class NodeConverter
{
    /**
     * @var SimpleXMLElement
     */
    protected $node;

    /**
     * @var ObjectManager
     */
    protected $om;

    public function __construct(SimpleXMLElement $node, ObjectManager $om)
    {
        $this->node = $node;
        $this->om = $om;
    }

    abstract public function convertToEntity();

    protected function findOneBy($className, array $criteria)
    {
        return $this->om->getRepository($className)->findOneBy($criteria);
    }
}
