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
    const POw_CHILD_NODE = 1;
    const GMI_CHILD_NODE = 2;
    const COMMUNITY_TYPE_CHILD_NODE = 3;
    const TYPE_CHILD_NODE = 4;
    const NAME_CHILD_NODE = 6;
    const ID_CHILD_NODE = 7;

    public function convertToEntity()
    {
        $place = new Place();
        $place->setId($this->getPlaceId())
            ->setName($this->getPlaceName())
            ->setCommunity($this->getPlaceCommunity())
            ->setType($this->getPlaceType());

        return $place;
    }

    /**
     * @return string
     */
    public function getDistrictCode()
    {
        return (string) $this->node->col[self::POw_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getProvinceCode()
    {
        return (string) $this->node->col[self::WOJ_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getCommunityCode()
    {
        return (string) $this->node->col[self::GMI_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getPlaceDictionaryType()
    {
        return (string) $this->node->col[self::TYPE_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getPlaceId()
    {
        return (string) $this->node->col[self::ID_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getPlaceName()
    {
        return (string) $this->node->col[self::NAME_CHILD_NODE];
    }

    /**
     * @return object
     */
    private function getPlaceCommunity()
    {
        return $this->om->getRepository('FSiTerytDbBundle:Community')->findOneBy(array(
            'code' => sprintf(
                "%s%s%s%s",
                $this->getProvinceCode(),
                $this->getDistrictCode(),
                $this->getCommunityCode(),
                (string) $this->node->col[self::COMMUNITY_TYPE_CHILD_NODE]
            )
        ));
    }

    /**
     * @return object
     */
    private function getPlaceType()
    {
        return $this->om->getRepository('FSiTerytDbBundle:PlaceType')->findOneBy(array(
            'type' => $this->getPlaceDictionaryType()
        ));
    }
}