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
     * @return Province
     */
    private function convertToProvince()
    {
        $provinceEntity = new Province();
        $provinceEntity->setName($this->getTerritoryName())
            ->setCode($this->getProvinceCode());

        return $provinceEntity;
    }

    /**
     * @return District
     */
    private function convertToDistrict()
    {
        $province = $this->om->getRepository('FSiTerytDbBundle:Province')->findOneBy(array(
            'code' => $this->getProvinceCode()
        ));

        $districtEntity = new District();
        $districtEntity->setCode(sprintf('%s%s', $this->getProvinceCode(), $this->getDistrictCode()))
            ->setName($this->getTerritoryName())
            ->setProvince($province);

        return $districtEntity;
    }

    /**
     * @return Community
     */
    private function convertToCommunity()
    {
        $district = $this->om->getRepository('FSiTerytDbBundle:District')->findOneBy(array(
            'code' => sprintf("%s%s", $this->getProvinceCode(), $this->getDistrictCode())
        ));

        $type = $this->om->getRepository('FSiTerytDbBundle:CommunityType')->findOneBy(array(
            'type' => (int) $this->node->col[self::TYPE_CHILD_NODE]
        ));

        $communityEntity = new Community();
        $communityEntity->setCode(sprintf(
            "%s%s%s%s",
            $this->getProvinceCode(),
            $this->getDistrictCode(),
            $this->getCommunityCode(),
            $this->getCommunityType()
        ))
            ->setName($this->getTerritoryName())
            ->setType($type)
            ->setDistrict($district);

        return $communityEntity;
    }

    /**
     * @return string
     */
    public function getDistrictCode()
    {
        return (string) $this->node->col[self::POW_CHILD_NODE];
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
        return (string) $this->node->col[self::WOJ_CHILD_NODE];
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
        return (string) $this->node->col[self::GMI_CHILD_NODE];
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
        return (string) $this->node->col[self::TYPE_CHILD_NODE];
    }
}