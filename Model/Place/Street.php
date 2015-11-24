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
     * @var int
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
     * @var Place
     */
    protected $place;

    /**
     * @param Place $place
     * @param int $id
     */
    public function __construct(Place $place, $id)
    {
        $this->place = $place;
        $this->id = $id;
    }

    /**
     * @return Place
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $additionalName
     * @return Street
     */
    public function setAdditionalName($additionalName)
    {
        $this->additionalName = $additionalName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalName()
    {
        return $this->additionalName;
    }

    /**
     * @param string $name
     * @return Street
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
     * @param string $type
     * @return Street
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

    /**
     * @return string
     */
    public function getFullName()
    {
        $fullName = array();

        if (!empty($this->type)) {
            $fullName[] = $this->type;
        }

        if (!empty($this->additionalName)) {
            $fullName[] = $this->additionalName;
        }

        if (!empty($this->name !== null)) {
            $fullName[] = $this->name;
        }

        return join(' ', $fullName);
    }
}
