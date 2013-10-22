<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\TerytDatabaseBundle\Model\Place;

class Street
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $additionalName;

    /**
     * @var \FSi\Bundle\TerytDatabaseBundle\Model\Place\Place
     */
    protected $place;

    /**
     * @param string $id
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Street
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
     * @param mixed $additionalName
     * @return Street
     */
    public function setAdditionalName($additionalName)
    {
        $this->additionalName = $additionalName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdditionalName()
    {
        return $this->additionalName;
    }

    /**
     * @param string $name
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Street
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
     * @param mixed $place
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Street
     */
    public function setPlace(Place $place)
    {
        $this->place = $place;
        return $this;
    }

    /**
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Place
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param mixed $type
     * @return \FSi\Bundle\TerytDatabaseBundle\Model\Place\Street
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}