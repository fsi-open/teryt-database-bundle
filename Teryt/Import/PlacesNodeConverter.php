<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use FSi\Bundle\TerytDatabaseBundle\Entity\Place;

class PlacesNodeConverter extends NodeConverter
{
    const WOJ_CHILD_NODE = 0;
    const POW_CHILD_NODE = 1;
    const GMI_CHILD_NODE = 2;
    const COMMUNITY_TYPE_CHILD_NODE = 3;
    const TYPE_CHILD_NODE = 4;
    const NAME_CHILD_NODE = 6;
    const ID_CHILD_NODE = 7;

    public function convertToEntity()
    {
        $placeEntity = $this->createPlaceEntity();
        $placeEntity->setName($this->getPlaceName())
            ->setCommunity($this->getPlaceCommunity())
            ->setType($this->getPlaceType());

        return $placeEntity;
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Entity\Place
     */
    private function createPlaceEntity()
    {
        return $this->findOneBy('FSiTerytDbBundle:Place', array(
            'id' => $this->getPlaceId()
        )) ?: new Place($this->getPlaceId());
    }

    /**
     * @return string
     */
    private function getDistrictCode()
    {
        return (int) $this->node->col[self::POW_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getProvinceCode()
    {
        return (int) $this->node->col[self::WOJ_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getCommunityCode()
    {
        return (int) $this->node->col[self::GMI_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getPlaceDictionaryType()
    {
        return (int) $this->node->col[self::TYPE_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getPlaceId()
    {
        return (int) $this->node->col[self::ID_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getPlaceName()
    {
        return (string) $this->node->col[self::NAME_CHILD_NODE];
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Entity\Community
     */
    private function getPlaceCommunity()
    {
        return $this->findOneBy('FSiTerytDbBundle:Community', array(
            'code' => (int) sprintf(
                "%d%02d%02d%1d",
                $this->getProvinceCode(),
                $this->getDistrictCode(),
                $this->getCommunityCode(),
                $this->node->col[self::COMMUNITY_TYPE_CHILD_NODE]
            )
        ));
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType
     */
    private function getPlaceType()
    {
        return $this->findOneBy('FSiTerytDbBundle:PlaceType', array(
            'type' => $this->getPlaceDictionaryType()
        ));
    }
}
