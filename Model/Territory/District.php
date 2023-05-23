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

class District extends Territory
{
    /**
     * @var Province
     */
    protected $province;

    /**
     * @var Collection<int, Community>
     */
    protected $communities;

    public function __construct(Province $province, int $code, string $name)
    {
        parent::__construct($code, $name);

        $this->province = $province;
        $this->communities = new ArrayCollection();
    }

    public function getProvince(): Province
    {
        return $this->province;
    }

    /**
     * @return Collection<int, Community>
     */
    public function getCommunities(): Collection
    {
        return $this->communities;
    }
}
