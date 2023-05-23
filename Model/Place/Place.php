<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle\Model\Place;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FSi\Bundle\TerytDatabaseBundle\Model\Territory\Community;

class Place
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var Community
     */
    protected $community;

    /**
     * @var Collection<int, Street>
     */
    protected $streets;

    /**
     * @var Place|null
     */
    protected $parentPlace;

    /**
     * @var Collection<int, Place>
     */
    protected $childPlaces;

    public function __construct(int $id, string $name, Type $type, Community $community)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->community = $community;
        $this->streets = new ArrayCollection();
        $this->childPlaces = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setCommunity(Community $community): void
    {
        $this->community = $community;
    }

    public function getCommunity(): Community
    {
        return $this->community;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setType(Type $type): void
    {
        $this->type = $type;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @return Collection<int, Street>
     */
    public function getStreets(): Collection
    {
        return $this->streets;
    }

    public function getParentPlace(): ?Place
    {
        return $this->parentPlace;
    }

    public function setParentPlace(?Place $parentPlace): void
    {
        $this->parentPlace = $parentPlace;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getChildPlaces(): Collection
    {
        return $this->childPlaces;
    }

    public function getFullName(): string
    {
        return sprintf('%s (%s)', $this->name, $this->type->getName());
    }
}
