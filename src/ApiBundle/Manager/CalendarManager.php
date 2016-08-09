<?php

namespace ApiBundle\Manager;

use ApiBundle\Entity\Calendar;
use ApiBundle\Entity\RoomType;
use Doctrine\ORM\EntityManager;

class CalendarManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Create a Calendar Entity
     *
     * @param RoomType $roomType
     * @param \DateTime $date
     * @param integer|null $availability
     * @param float|null $price
     * @return Calendar
     */
    public function createCalendar(RoomType $roomType, \DateTime $date, $availability = null, $price = null)
    {
        $calendar = new Calendar();
        $calendar->setRoomType($roomType);
        $calendar->setDay($date);
        $calendar->setAvailability((null !== $availability) ? $availability : $roomType->getDefaultAvailability());
        $calendar->setPrice((null !== $price) ? $price : $roomType->getDefaultPrice());

        return $calendar;
    }

    /**
     * Save (persist and flush) Calendar entity to Database
     *
     * @param Calendar $calendar
     * @return $this
     */
    public function save(Calendar $calendar)
    {
        $this->em->persist($calendar);
        $this->em->flush($calendar);

        return $this;
    }

    /**
     * Bulk Save (persist and flush) Calendar entities to Database
     *
     * @param Calendar[] $calendar
     * @return $this
     */
    public function bulkSave($calendar)
    {
        foreach ($calendar as $calendarEntry) {
            $this->em->persist($calendarEntry);
        }
        $this->em->flush();

        return $this;
    }
}
