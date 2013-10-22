<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType;

class PlacesDictionaryNodeConverter extends NodeConverter
{
    const TYPE_CHILD_NODE = 0;
    const TYPE_NAME_CHILD_NODE = 1;

    public function convertToEntity()
    {
        $placeType = new PlaceType();
        $placeType->setType($this->node->col[self::TYPE_CHILD_NODE])
            ->setName(trim($this->node->col[self::TYPE_NAME_CHILD_NODE]));

        return $placeType;
    }
}