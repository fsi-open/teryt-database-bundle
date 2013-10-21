<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Model\Place;

use Doctrine\Common\Collections\ArrayCollection;

class Type
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $places;

    function __construct()
    {
        $this->places = new ArrayCollection();
    }

    /**
     * @param mixed $type
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $name
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Type
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection
     */
    public function getPlaces()
    {
        return $this->places;
    }
}