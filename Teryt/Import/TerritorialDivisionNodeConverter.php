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
use FSi\Bundle\TerytDatabaseBundle\Entity\CommunityType;
use FSi\Bundle\TerytDatabaseBundle\Entity\District;
use FSi\Bundle\TerytDatabaseBundle\Entity\Province;
use FSi\Bundle\TerytDatabaseBundle\Exception\TerritorialDivisionNodeConverterException;
use RuntimeException;

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

        throw new TerritorialDivisionNodeConverterException('Unknown territory type');
    }

    private function isProvinceNode(): bool
    {
        return $this->hasProvinceCode() && !$this->hasDistrictCode();
    }

    private function isDistrict(): bool
    {
        return $this->hasProvinceCode() && $this->hasDistrictCode() && !$this->hasCommunityCode();
    }

    public function isCommunity(): bool
    {
        return $this->hasProvinceCode() && $this->hasDistrictCode() && $this->hasCommunityCode();
    }

    private function convertToProvince(): Province
    {
        /** @var Province|null $provinceEntity */
        $provinceEntity = $this->findOneBy(Province::class, ['code' => $this->getProvinceCode()]);
        if ($provinceEntity === null) {
            return new Province($this->getProvinceCode(), $this->getTerritoryName());
        }

        $provinceEntity->setName($this->getTerritoryName());

        return $provinceEntity;
    }

    private function convertToDistrict(): District
    {
        /** @var Province|null $province */
        $province = $this->findOneBy(Province::class, ['code' => $this->getProvinceCode()]);
        if ($province === null) {
            throw new RuntimeException(sprintf('Unable to find province "%s"', $this->getProvinceCode()));
        }

        $districtCode = (int) sprintf('%d%02d', $this->getProvinceCode(), $this->getDistrictCode());
        /** @var District|null $district */
        $district = $this->findOneBy(District::class, ['code' => $districtCode]);

        if ($district === null) {
            return new District($province, $districtCode, $this->getTerritoryName());
        }

        $district->setName($this->getTerritoryName());

        return $district;
    }

    private function convertToCommunity(): Community
    {
        $districtCode = (int) sprintf('%1d%02d', $this->getProvinceCode(), $this->getDistrictCode());
        /** @var District|null $district */
        $district = $this->findOneBy(District::class, ['code' => $districtCode]);
        if ($district === null) {
            throw new RuntimeException(sprintf('Unable to find district "%s"', $districtCode));
        }

        /** @var CommunityType|null $type */
        $type = $this->findOneBy(CommunityType::class, ['type' => (int) $this->node->rodz->__toString()]);
        if ($type === null) {
            throw new RuntimeException(sprintf('Unable to find community type "%s"', $this->node->rodz->__toString()));
        }

        $communityCode = (int) sprintf(
            '%d%02d%02d%1d',
            $this->getProvinceCode(),
            $this->getDistrictCode(),
            $this->getCommunityCode(),
            $this->getCommunityType()
        );
        /** @var Community|null $community */
        $community = $this->findOneBy(Community::class, ['code' => $communityCode]);

        if ($community === null) {
            return new Community($district, $communityCode, $this->getTerritoryName(), $type);
        }

        $community->setName($this->getTerritoryName());
        $community->setType($type);

        return $community;
    }

    public function getDistrictCode(): int
    {
        return (int) $this->node->pow->__toString();
    }

    private function hasProvinceCode(): bool
    {
        return !empty(trim($this->node->woj->__toString()));
    }

    private function getProvinceCode(): int
    {
        return (int) $this->node->woj->__toString();
    }

    public function hasDistrictCode(): bool
    {
        return !empty(trim($this->node->pow->__toString()));
    }

    public function hasCommunityCode(): bool
    {
        return !empty(trim($this->node->gmi->__toString()));
    }

    private function getCommunityCode(): int
    {
        return (int) $this->node->gmi->__toString();
    }

    private function getTerritoryName(): string
    {
        return (string) $this->node->nazwa;
    }

    private function getCommunityType(): int
    {
        return (int) $this->node->rodz->__toString();
    }
}
