<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Model\Territory;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FSi\Bundle\TerytDatabaseBundle\Model\Place\Place;

class Community extends Territory
{
    /**
     * @var District
     */
    protected $district;

    /**
     * @var CommunityType
     */
    protected $type;

    /**
     * @var Collection<int, Place>
     */
    protected $places;

    public function __construct(District $district, int $code, string $name, CommunityType $type)
    {
        parent::__construct($code, $name);

        $this->district = $district;
        $this->type = $type;
        $this->places = new ArrayCollection();
    }

    public function getDistrict(): District
    {
        return $this->district;
    }

    public function setType(CommunityType $type): void
    {
        $this->type = $type;
    }

    public function getType(): CommunityType
    {
        return $this->type;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function getFullName(): string
    {
        return sprintf('%s (%s)', $this->getName(), $this->type->getName());
    }
}
