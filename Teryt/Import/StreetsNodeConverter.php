<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use FSi\Bundle\TerytDatabaseBundle\Entity\Place;
use FSi\Bundle\TerytDatabaseBundle\Entity\Street;

class StreetsNodeConverter extends NodeConverter
{
    public function convertToEntity(): Street
    {
        $place = $this->getPlace();

        /** @var Street|null $streetEntity */
        $streetEntity = $this->findOneBy(Street::class, [
            'id' => $this->getStreetId(),
            'place' => $place
        ]);

        if ($streetEntity === null) {
            return new Street(
                $place,
                $this->getStreetId(),
                $this->getStreetType(),
                $this->getAdditionalName(),
                $this->getName()
            );
        }

        $streetEntity->setType($this->getStreetType());
        $streetEntity->setName($this->getName());
        $streetEntity->setAdditionalName($this->getAdditionalName());

        return $streetEntity;
    }

    private function getStreetId(): int
    {
        return (int) $this->node->sym_ul->__toString();
    }

    private function getName(): string
    {
        return trim((string) $this->node->nazwa_1);
    }

    private function getAdditionalName(): ?string
    {
        $additionalName = trim((string) $this->node->nazwa_2);

        return $additionalName ?: null;
    }

    private function getStreetType(): string
    {
        return (string) $this->node->cecha;
    }

    private function getPlace(): Place
    {
        return $this->om->getRepository(Place::class)->findOneBy([
            'id' => (int) $this->node->sym->__toString()
        ]);
    }
}
