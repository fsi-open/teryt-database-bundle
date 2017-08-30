<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use FSi\Bundle\TerytDatabaseBundle\Entity\Place;
use FSi\Bundle\TerytDatabaseBundle\Entity\Street;

class StreetsNodeConverter extends NodeConverter
{
    public function convertToEntity()
    {
        $streetEntity = $this->createStreetEntity();
        $streetEntity->setName($this->getName())
            ->setAdditionalName($this->getAdditionalName())
            ->setType($this->getStreetType());

        return $streetEntity;
    }

    /**
     * @return Street
     */
    private function createStreetEntity()
    {
        $place = $this->getPlace();

        return $this->findOneBy('FSiTerytDbBundle:Street', array(
            'id' => $this->getStreetId(),
            'place' => $place
        )) ?: new Street($place, $this->getStreetId());
    }

    /**
     * @return string
     */
    private function getStreetId()
    {
        return (int) $this->node->sym_ul->__toString();
    }

    /**
     * @return string
     */
    private function getName()
    {
        return trim((string) $this->node->nazwa_1);
    }

    /**
     * @return string
     */
    private function getAdditionalName()
    {
        $additionalName = trim((string) $this->node->nazwa_2);

        return $additionalName ?: null;
    }

    /**
     * @return string
     */
    private function getStreetType()
    {
        return (string) $this->node->cecha;
    }

    /**
     * @return Place
     */
    private function getPlace()
    {
        return $this->om->getRepository(Place::class)->findOneBy(array(
            'id' => (int) $this->node->sym->__toString()
        ));
    }
}
