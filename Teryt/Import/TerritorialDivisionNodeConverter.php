<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use FSi\Bundle\TerytDatabaseBundle\Entity\Community;
use FSi\Bundle\TerytDatabaseBundle\Entity\CommunityType;
use FSi\Bundle\TerytDatabaseBundle\Entity\District;
use FSi\Bundle\TerytDatabaseBundle\Entity\Province;
use FSi\Bundle\TerytDatabaseBundle\Exception\TerritorialDivisionNodeConverterException;

class TerritorialDivisionNodeConverter extends NodeConverter
{
    /**
     * @throws TerritorialDivisionNodeConverterException
     * @return Community|District|Province
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
        $provinceEntity = $this->createProvinceEntity();
        $provinceEntity->setName($this->getTerritoryName());

        return $provinceEntity;
    }

    /**
     * @return District
     */
    private function convertToDistrict()
    {
        $province = $this->findOneBy(Province::class, array(
            'code' => $this->getProvinceCode()
        ));

        return $this->createDistrictEntity()
            ->setName($this->getTerritoryName())
            ->setProvince($province);
    }

    /**
     * @return Community
     */
    private function convertToCommunity()
    {
        $district = $this->findOneBy(District::class, array(
            'code' => (int) sprintf('%1d%02d', $this->getProvinceCode(), $this->getDistrictCode())
        ));

        $type = $this->findOneBy(CommunityType::class, array(
            'type' => (int) $this->node->rodz->__toString()
        ));

        $community = $this->createCommunityEntity();
        $community->setName($this->getTerritoryName());
        $community->setType($type);
        $community->setDistrict($district);

        return $community;
    }

    /**
     * @return Province
     */
    private function createProvinceEntity()
    {
        return $this->findOneBy(Province::class, array(
            'code' => $this->getProvinceCode()
        )) ?: new Province($this->getProvinceCode());
    }

    /**
     * @return District
     */
    private function createDistrictEntity()
    {
        $districtCode = (int) sprintf('%d%02d', $this->getProvinceCode(), $this->getDistrictCode());

        return $this->findOneBy(District::class, array(
            'code' => $districtCode
        )) ?: new District($districtCode);
    }

    /**
     * @return Community
     */
    private function createCommunityEntity()
    {
        $communityCode = (int) sprintf(
            '%d%02d%02d%1d',
            $this->getProvinceCode(),
            $this->getDistrictCode(),
            $this->getCommunityCode(),
            $this->getCommunityType()
        );

        return $this->findOneBy(Community::class, array(
            'code' => $communityCode
        )) ?: new Community($communityCode);
    }

    /**
     * @return int
     */
    public function getDistrictCode()
    {
        return (int) $this->node->pow->__toString();
    }

    /**
     * @return bool
     */
    private function hasProvinceCode()
    {
        return !empty(trim($this->node->woj->__toString()));
    }

    /**
     * @return int
     */
    private function getProvinceCode()
    {
        return (int) $this->node->woj->__toString();
    }

    /**
     * @return bool
     */
    public function hasDistrictCode()
    {
        return !empty(trim($this->node->pow->__toString()));
    }

    /**
     * @return bool
     */
    public function hasCommunityCode()
    {
        return !empty(trim($this->node->gmi->__toString()));
    }

    /**
     * @return int
     */
    private function getCommunityCode()
    {
        return (int) $this->node->gmi->__toString();
    }

    /**
     * @return string
     */
    private function getTerritoryName()
    {
        return (string) $this->node->nazwa;
    }

    /**
     * @return int
     */
    private function getCommunityType()
    {
        return (int) $this->node->rodz->__toString();
    }
}
