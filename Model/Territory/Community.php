<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Model\Territory;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FSi\Bundle\TerytDatabaseBundle\Model\Place\Place;

class Community extends Territory
{
    /**
     * @var District
     */
    protected $district;

    /**
     * @var CommunityType
     */
    protected $type;

    /**
     * @var Collection|Place[]
     */
    protected $places;

    /**
     * @param int $code
     */
    function __construct($code)
    {
        parent::__construct($code);
        $this->places = new ArrayCollection();
    }

    /**
     * @param District $district
     * @return Community
     */
    public function setDistrict(District $district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * @return District
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param CommunityType $type
     * @return Community
     */
    public function setType(CommunityType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return CommunityType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Collection|Place[]
     */
    public function getPlaces()
    {
        return $this->places;
    }
}
