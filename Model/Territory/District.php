<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Model\Territory;

use Doctrine\Common\Collections\ArrayCollection;
use FSi\Bundle\TerytDatabaseBundle\Model\Territory\Province;
use FSi\Bundle\TerytDatabaseBundle\Model\Territory\Territory;

class District extends Territory
{
    /**
     * @var \FSi\Bundle\TerytDatabaseBundle\Model\Territory\Province
     */
    protected $province;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $communities;

    function __construct()
    {
        $this->communities = new ArrayCollection();
    }

    /**
     * @param \FSi\Bundle\TerytDatabaseBundle\Model\Territory\Province $province
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Territory\District
     */
    public function setProvince(Province $province)
    {
        $this->province = $province;

        return $this;
    }

    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCommunities()
    {
        return $this->communities;
    }
}