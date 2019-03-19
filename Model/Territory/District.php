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
     * @var Collection|Community[]
     */
    protected $communities;

    /**
     * @param int $code
     */
    function __construct($code)
    {
        parent::__construct($code);
        $this->communities = new ArrayCollection();
    }

    /**
     * @param Province $province
     * @return District
     */
    public function setProvince(Province $province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * @return Province
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @return Collection|Community[]
     */
    public function getCommunities()
    {
        return $this->communities;
    }
}
