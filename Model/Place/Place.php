<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Model\Place;

use Doctrine\Common\Collections\ArrayCollection;
use FSi\Bundle\TerytDatabaseBundle\Model\Territory\Community;

class Place
{
    protected $id;

    protected $name;

    protected $type;

    protected $community;

    protected $streets;

    function __construct()
    {
        $this->streets = new ArrayCollection();
    }

    /**
     * @param string $id
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Place
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \FSi\Bundle\TerytDatabaseBundle\Model\Territory\Community $community
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Place
     */
    public function setCommunity(Community $community)
    {
        $this->community = $community;
        return $this;
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Territory\Community
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     * @param string $name
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Place
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
     * @param \FSi\Bundle\TerytDatabaseBundle\Model\Place\Type $type
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Place
     */
    public function setType(Type $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getStreets()
    {
        return $this->streets;
    }
}