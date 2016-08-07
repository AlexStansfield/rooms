<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RoomType
 *
 * @ORM\Table(name="room_type")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\RoomTypeRepository")
 */
class RoomType
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, unique=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="default_availability", type="integer")
     */
    private $defaultAvailability;

    /**
     * @var string
     *
     * @ORM\Column(name="default_price", type="decimal", precision=10, scale=2)
     */
    private $defaultPrice;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return RoomType
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return RoomType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set defaultAvailability
     *
     * @param integer $defaultAvailability
     *
     * @return RoomType
     */
    public function setDefaultAvailability($defaultAvailability)
    {
        $this->defaultAvailability = $defaultAvailability;

        return $this;
    }

    /**
     * Get defaultAvailability
     *
     * @return int
     */
    public function getDefaultAvailability()
    {
        return $this->defaultAvailability;
    }

    /**
     * Set defaultPrice
     *
     * @param string $defaultPrice
     *
     * @return RoomType
     */
    public function setDefaultPrice($defaultPrice)
    {
        $this->defaultPrice = $defaultPrice;

        return $this;
    }

    /**
     * Get defaultPrice
     *
     * @return string
     */
    public function getDefaultPrice()
    {
        return $this->defaultPrice;
    }
}

