<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use FSi\Bundle\TerytDatabaseBundle\Entity\Community;
use FSi\Bundle\TerytDatabaseBundle\Entity\Place;
use FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType;

class PlacesNodeConverter extends NodeConverter
{
    public function convertToEntity()
    {
        $placeEntity = $this->createPlaceEntity();
        $placeEntity->setName($this->getPlaceName())
            ->setCommunity($this->getPlaceCommunity())
            ->setType($this->getPlaceType())
            ->setParentPlace($this->getParentPlace());

        return $placeEntity;
    }

    /**
     * @return Place
     */
    private function createPlaceEntity()
    {
        return $this->findOneBy(Place::class, array(
            'id' => $this->getPlaceId()
        )) ?: new Place($this->getPlaceId());
    }

    /**
     * @return int
     */
    private function getDistrictCode()
    {
        return (int) $this->node->pow->__toString();
    }

    /**
     * @return int
     */
    private function getProvinceCode()
    {
        return (int) $this->node->woj->__toString();
    }

    /**
     * @return int
     */
    private function getCommunityCode()
    {
        return (int) $this->node->gmi->__toString();
    }

    /**
     * @return int
     */
    private function getPlaceDictionaryType()
    {
        return (int) $this->node->rm->__toString();
    }

    /**
     * @return int
     */
    private function getPlaceId()
    {
        return (int) $this->node->sym->__toString();
    }

    /**
     * @return int
     */
    private function getParentPlaceId()
    {
        return (int) $this->node->sympod->__toString();
    }

    /**
     * @return string
     */
    private function getPlaceName()
    {
        return (string) $this->node->nazwa->__toString();
    }

    /**
     * @return Community
     */
    private function getPlaceCommunity()
    {
        return $this->findOneBy(Community::class, array(
            'code' => (int) sprintf(
                "%d%02d%02d%1d",
                $this->getProvinceCode(),
                $this->getDistrictCode(),
                $this->getCommunityCode(),
                $this->node->rodz_gmi->__toString()
            )
        ));
    }

    /**
     * @return PlaceType
     */
    private function getPlaceType()
    {
        return $this->findOneBy(PlaceType::class, array(
            'type' => $this->getPlaceDictionaryType()
        ));
    }

    /**
     * @return Place
     */
    private function getParentPlace()
    {
        if ($this->getParentPlaceId() && ($this->getParentPlaceId() !== $this->getPlaceId())) {
            return $this->findOneBy(Place::class, [
                'id' => $this->getParentPlaceId()
            ]);
        }

        return null;
    }
}
