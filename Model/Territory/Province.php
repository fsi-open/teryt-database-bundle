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

class Province extends Territory
{
    /**
     * @var Collection<int, District>
     */
    protected $districts;

    public function __construct(int $code, string $name)
    {
        parent::__construct($code, $name);

        $this->districts = new ArrayCollection();
    }

    /**
     * @return Collection<int, District>
     */
    public function getDistricts(): Collection
    {
        return $this->districts;
    }
}
