<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Model\Territory;

use Doctrine\Common\Collections\ArrayCollection;
use FSi\Bundle\TerytDatabaseBundle\Model\Territory\District;
use FSi\Bundle\TerytDatabaseBundle\Model\Territory\Territory;

class Community extends Territory
{
    /**
     * @var \FSi\Bundle\TerytDatabaseBundle\Model\Territory\District
     */
    protected $district;

    /**
     * @var \FSi\Bundle\TerytDatabaseBundle\Model\Territory\CommunityType
     */
    protected $type;

    protected $places;

    function __construct()
    {
        $this->places = new ArrayCollection();
    }

    /**
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

    /**
     * @param \FSi\Bundle\TerytDatabaseBundle\Model\Territory\CommunityType $type
     * @return Community
     */
    public function setType(CommunityType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Territory\CommunityType
     */
    public function getType()
    {
        return $this->type;
    }

    public function getPlaces()
    {
        return $this->places;
    }
}