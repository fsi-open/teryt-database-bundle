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

class CommunityType
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
     * @var Collection|Community[]
     */
    protected $communities;

    /**
     * @param int $type
     */
    public function __construct($type)
    {
        $this->type = $type;
        $this->communities = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $name
     * @return CommunityType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Collection|Community[]
     */
    public function getCommunities()
    {
        return $this->communities;
    }
}
