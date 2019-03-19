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
    public function convertToEntity(): Place
    {
        /** @var Place|null $placeEntity */
        $placeEntity = $this->findOneBy(Place::class, ['id' => $this->getPlaceId()]);

        if ($placeEntity !== null) {
            $placeEntity->setName($this->getPlaceName());
            $placeEntity->setType($this->getPlaceType());
            $placeEntity->setCommunity($this->getPlaceCommunity());
        } else {
            $placeEntity = new Place(
                $this->getPlaceId(),
                $this->getPlaceName(),
                $this->getPlaceType(),
                $this->getPlaceCommunity()
            );
        }

        $placeEntity->setParentPlace($this->getParentPlace());

        return $placeEntity;
    }

    private function getDistrictCode(): int
    {
        return (int) $this->node->pow->__toString();
    }

    private function getProvinceCode(): int
    {
        return (int) $this->node->woj->__toString();
    }

    private function getCommunityCode(): int
    {
        return (int) $this->node->gmi->__toString();
    }

    private function getPlaceDictionaryType(): int
    {
        return (int) $this->node->rm->__toString();
    }

    private function getPlaceId(): int
    {
        return (int) $this->node->sym->__toString();
    }

    private function getParentPlaceId(): int
    {
        return (int) $this->node->sympod->__toString();
    }

    private function getPlaceName(): string
    {
        return (string) $this->node->nazwa->__toString();
    }

    private function getPlaceCommunity(): Community
    {
        return $this->findOneBy(Community::class, [
            'code' => (int) sprintf(
                '%d%02d%02d%1d',
                $this->getProvinceCode(),
                $this->getDistrictCode(),
                $this->getCommunityCode(),
                $this->node->rodz_gmi->__toString()
            )
        ]);
    }

    private function getPlaceType(): PlaceType
    {
        return $this->findOneBy(PlaceType::class, ['type' => $this->getPlaceDictionaryType()]);
    }

    private function getParentPlace(): ?Place
    {
        if ($this->getParentPlaceId() && ($this->getParentPlaceId() !== $this->getPlaceId())) {
            return $this->findOneBy(Place::class, ['id' => $this->getParentPlaceId()]);
        }

        return null;
    }
}
