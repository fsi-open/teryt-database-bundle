<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType;
use SimpleXMLElement;

class PlacesDictionaryNodeConverter extends NodeConverter
{
    public function convertToEntity()
    {
        $placeType = $this->createPlaceTypeEntity();
        $placeType->setName($this->getPlaceName());

        return $placeType;
    }

    /**
     * @return PlaceType
     */
    private function createPlaceTypeEntity()
    {
        return $this->findOneBy(PlaceType::class, array(
            'type' => $this->getPlaceType()
        )) ?: new PlaceType($this->getPlaceType());
    }

    /**
     * @return int
     */
    private function getPlaceType()
    {
        return (int) $this->node->rm->__toString();
    }

    /**
     * @return string
     */
    private function getPlaceName()
    {
        return trim((string) $this->node->nazwa_rm->__toString());
    }
}
