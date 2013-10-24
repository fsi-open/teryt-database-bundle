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
        $placeType = $this->createPlaceTypeEntity();
        $placeType->setName($this->getPlaceName());

        return $placeType;
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType
     */
    private function createPlaceTypeEntity()
    {
        $placeType = $this->om->getRepository('FSiTerytDbBundle:PlaceType')->findOneBy(array(
            'type' => $this->getPlaceType()
        ));

        if (!isset($placeType)) {
            $placeType = new PlaceType();
            $placeType->setType($this->getPlaceType());
            return $placeType;
        }
        return $placeType;
    }

    /**
     * @return \SimpleXMLElement
     */
    private function getPlaceType()
    {
        return (string) $this->node->col[self::TYPE_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getPlaceName()
    {
        return trim((string) $this->node->col[self::TYPE_NAME_CHILD_NODE]);
    }
}