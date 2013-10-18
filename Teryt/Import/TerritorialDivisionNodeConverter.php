<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use Doctrine\Common\Persistence\ObjectManager;
use FSi\Bundle\TerytDatabaseBundle\Entity\Community;
use FSi\Bundle\TerytDatabaseBundle\Entity\District;
use FSi\Bundle\TerytDatabaseBundle\Entity\Province;
use FSi\Bundle\TerytDatabaseBundle\Exception\TerritorialDivisionNodeConverterException;

class TerritorialDivisionNodeConverter
{
    const WOJ_CHILD_NODE = 0;
    const POw_CHILD_NODE = 1;
    const GMI_CHILD_NODE = 2;
    const RODZ_CHILD_NODE = 3;
    const NAZWA_CHILD_NODE = 4;

    /**
     * @var \SimpleXMLElement
     */
    private $node;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;

    public function __construct(\SimpleXMLElement $node, ObjectManager $om)
    {
        $this->node = $node;
        $this->om = $om;
    }

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

        $communityEntity = new Community();
        $communityEntity->setCode(sprintf(
            "%s%s%s",
            $this->getProvinceCode(),
            $this->getDistrictCode(),
            $this->getCommunityCode()
        ))
            ->setName($this->getTerritoryName())
            ->setDistrict($district);

        return $communityEntity;
    }

    /**
     * @return string
     */
    public function getDistrictCode()
    {
        return (string) $this->node->col[self::POw_CHILD_NODE];
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
        return !empty($this->node->col[self::POw_CHILD_NODE]);
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
        return (string)$this->node->col[self::GMI_CHILD_NODE];
    }

    /**
     * @return string
     */
    private function getTerritoryName()
    {
        return (string) $this->node->col[self::NAZWA_CHILD_NODE];
    }
}