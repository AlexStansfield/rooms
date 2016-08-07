<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Calendar
 *
 * @ORM\Table(
 *     name="calendar",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="room_date_unique", columns={"day", "room_type_id"})}
 *     )
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\CalendarRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Calendar
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
     * @var \DateTime
     *
     * @ORM\Column(name="day", type="date")
     * @JMS\Expose
     * @JMS\Type("DateTime<'Y-m-d'>")
     */
    private $day;

    /**
     * @var RoomType
     *
     * @ORM\ManyToOne(targetEntity="RoomType")
     * @ORM\JoinColumn(name="room_type_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     * @JMS\Accessor(getter="getRoomTypeString")
     * @JMS\Type("string")
     */
    private $roomType;

    /**
     * @var int
     *
     * @ORM\Column(name="availability", type="integer")
     * @JMS\Expose
     */
    private $availability;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     * @JMS\Expose
     */
    private $price;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;


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
     * Set date
     *
     * @param \DateTime $day
     *
     * @return Calendar
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set roomType
     *
     * @param RoomType $roomType
     *
     * @return Calendar
     */
    public function setRoomType($roomType)
    {
        $this->roomType = $roomType;

        return $this;
    }

    /**
     * Get roomType
     *
     * @return RoomType
     */
    public function getRoomType()
    {
        return $this->roomType;
    }

    /**
     * Set availability
     *
     * @param integer $availability
     *
     * @return Calendar
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * Get availability
     *
     * @return int
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Calendar
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Calendar
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return string
     */
    public function getRoomTypeString()
    {
        return $this->roomType->getType();
    }
}

