<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Model\Territory;

use FSi\Bundle\TerytDatabaseBundle\Model\Territory\District;
use FSi\Bundle\TerytDatabaseBundle\Model\Territory\Territory;

class Community extends Territory
{
    /**
     * @var \FSi\Bundle\TerytDatabaseBundle\Model\Territory\District
     */
    protected $district;

    /**
     *
     * @param \FSi\Bundle\TerytDatabaseBundle\Model\Territory\District $district
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Territory\Community
     */
    public function setDistrict(District $district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Territory\District
     */
    public function getDistrict()
    {
        return $this->district;
    }
}