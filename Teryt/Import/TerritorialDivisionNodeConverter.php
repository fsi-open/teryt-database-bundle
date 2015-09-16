<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use FSi\Bundle\TerytDatabaseBundle\Entity\Community;
use FSi\Bundle\TerytDatabaseBundle\Entity\District;
use FSi\Bundle\TerytDatabaseBundle\Entity\Province;
use FSi\Bundle\TerytDatabaseBundle\Exception\TerritorialDivisionNodeConverterException;

class TerritorialDivisionNodeConverter extends NodeConverter
{
    const WOJ_CHILD_NODE = 0;
    const POW_CHILD_NODE = 1;
    const GMI_CHILD_NODE = 2;
    const TYPE_CHILD_NODE = 3;
    const NAZWA_CHILD_NODE = 4;

    /**
     * @throws \FSi\Bundle\TerytDatabaseBundle\Exception\TerritorialDivisionNodeConverterException
     * @return \FSi\Bundle\TerytDatabaseBundle\Entity\Community|\FSi\Bundle\TerytDatabaseBundle\Entity\District|\FSi\Bundle\TerytDatabaseBundle\Entity\Province
     */
    public function convertToEntity()
    {
        if ($this->isProvinceNode()) {
            return $this->convertToProvince();
        }

        if ($this->isDistrict()) {
            return $this->convertToDistrict();
        }

        if ($this->isCommunity()) {
            return $this->convertToCommunity();
        }

        throw new TerritorialDivisionNodeConverterException();
    }

    /**
     * @return bool
     */
    private function isProvinceNode()
    {
        return $this->hasProvinceCode() && !$this->hasDistrictCode();
    }

    /**
     * @return bool
     */
    private function isDistrict()
    {
        return $this->hasProvinceCode() && $this->hasDistrictCode() && !$this->hasCommunityCode();
    }

    /**
     * @return bool
     */
    public function isCommunity()
    {
        return $this->hasProvinceCode() && $this->hasDistrictCode() && $this->hasCommunityCode();
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Entity\Province
     */
    private function convertToProvince()
    {
        $provinceEntity = $this->createProvinceEntity();
        $provinceEntity->setName($this->getTerritoryName());

        return $provinceEntity;
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Entity\District
     */
    private function convertToDistrict()
    {
        $province = $this->findOneBy('FSiTerytDbBundle:Province', array(
            'code' => $this->getProvinceCode()
        ));

        return $this->createDistrictEntity()
            ->setName($this->getTerritoryName())
            ->setProvince($province);
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Entity\Community
     */
    private function convertToCommunity()
    {
        $district = $this->findOneBy('FSiTerytDbBundle:District', array(
            'code' => (int) sprintf("%1d%02d", $this->getProvinceCode(), $this->getDistrictCode())
        ));

        $type = $this->findOneBy('FSiTerytDbBundle:CommunityType', array(
            'type' => (int) $this->node->col[self::TYPE_CHILD_NODE]
        ));

        return $this->createCommunityEntity()
            ->setName($this->getTerritoryName())
            ->setType($type)
            ->setDistrict($district);
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Entity\Province
     */
    private function createProvinceEntity()
    {
        return $this->findOneBy('FSiTerytDbBundle:Province', array(
            'code' => $this->getProvinceCode()
        )) ?: new Province($this->getProvinceCode());
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Entity\District
     */
    private function createDistrictEntity()
    {
        $districtCode = (int) sprintf('%d%02d', $this->getProvinceCode(), $this->getDistrictCode());

        return $this->findOneBy('FSiTerytDbBundle:District', array(
            'code' => $districtCode
        )) ?: new District($districtCode);
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Entity\Community
     */
    private function createCommunityEntity()
    {
        $communityCode = (int) sprintf(
            "%d%02d%02d%1d",
            $this->getProvinceCode(),
            $this->getDistrictCode(),
            $this->getCommunityCode(),
            $this->getCommunityType()
        );

        return $this->findOneBy('FSiTerytDbBundle:Community', array(
            'code' => $communityCode
        )) ?: new Community($communityCode);
    }

    /**
     * @return string
     */
    public function getDistrictCode()
    {
        return (int) $this->node->col[self::POW_CHILD_NODE];
    }

    /**
     * @return bool
     */
    private function hasProvinceCode()
    {
        return !empty($this->node->col[self::WOJ_CHILD_NODE]);
    }

    /**
     * @return string
     */
    private function getProvinceCode()
    {
        return (int) $this->node->col[self::WOJ_CHILD_NODE];
    }

    /**
     * @return bool
     */
    public function hasDistrictCode()
    {
        return !empty($this->node->col[self::POW_CHILD_NODE]);
    }

    /**
     * @return bool
     */
    public function hasCommunityCode()
    {
        return !empty($this->node->col[self::GMI_CHILD_NODE]);
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
    private function getTerritoryName()
    {
        return (string) $this->node->col[self::NAZWA_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getCommunityType()
    {
        return (int) $this->node->col[self::TYPE_CHILD_NODE];
    }
}
