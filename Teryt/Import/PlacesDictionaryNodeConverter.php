<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType;

class PlacesDictionaryNodeConverter extends NodeConverter
{
    public function convertToEntity(): PlaceType
    {
        /** @var PlaceType|null $placeType */
        $placeType = $this->findOneBy(PlaceType::class, ['type' => $this->getPlaceType()]);

        if ($placeType === null) {
            return new PlaceType($this->getPlaceType(), $this->getPlaceTypeName());
        }

        $placeType->setName($this->getPlaceTypeName());

        return $placeType;
    }

    private function getPlaceType(): int
    {
        return (int) $this->node->rm->__toString();
    }

    private function getPlaceTypeName(): string
    {
        return trim((string) $this->node->nazwa_rm->__toString());
    }
}
