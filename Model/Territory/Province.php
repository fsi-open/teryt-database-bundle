<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Model\Territory;

use Doctrine\Common\Collections\ArrayCollection;
use FSi\Bundle\TerytDatabaseBundle\Model\Territory\Territory;

class Province extends Territory
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $districts;

    public function __construct()
    {
        $this->districts = new ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDistricts()
    {
        return $this->districts;
    }
}