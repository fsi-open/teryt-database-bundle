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

class Type
{
    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Collection<int, Place>
     */
    protected $places;

    public function __construct(int $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
        $this->places = new ArrayCollection();
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }
}
