<?php

namespace ApiBundle\Service;

use ApiBundle\Entity\Calendar;
use ApiBundle\Entity\RoomType;
use ApiBundle\Repository\CalendarRepository;

/**
 * Class CalendarService
 * @package ApiBundle\Service
 */
class CalendarService
{
    /** @var CalendarRepository */
    private $calendarRepo;

    /**
     * CalendarService constructor
     *
     * @param CalendarRepository $calendarRepo
     */
    public function __construct(CalendarRepository $calendarRepo)
    {
        $this->calendarRepo = $calendarRepo;
    }

    /**
     * Get Calendar by Date Range
     *
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @return \ApiBundle\Entity\Calendar[]
     */
    public function getCalendarByDateRange(\DateTime $dateFrom, \DateTime $dateTo)
    {
        return $this->calendarRepo->findByDateRange($dateFrom, $dateTo);
    }

    /**
     * Add missing Room data to Calendar
     *
     * @param Calendar[] $calendar
     * @param array $missingDayRooms
     * @return Calendar[]
     */
    public function addMissingDayRooms($calendar, array $missingDayRooms)
    {
        foreach ($missingDayRooms as $day => $dayRoomTypes) {
            /** @var RoomType[] $dayRoomTypes */
            $dayDate = \DateTime::createFromFormat('Y-m-d', $day);

            foreach ($dayRoomTypes as $dayRoomType) {
                $newDayRoom = new Calendar();
                $newDayRoom->setRoomType($dayRoomType);
                $newDayRoom->setAvailability($dayRoomType->getDefaultAvailability());
                $newDayRoom->setPrice($dayRoomType->getDefaultPrice());
                $newDayRoom->setDay($dayDate);

                $calendar[] = $newDayRoom;
            }
        }

        return $calendar;
    }

    /**
     * Determine which dates and rooms are missing from the calendar
     *
     * @param Calendar[] $calendar
     * @param RoomType[] $roomTypes
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @return array
     */
    public function determineMissingDayRooms($calendar, $roomTypes, \DateTime $dateFrom, \DateTime $dateTo)
    {
        // Create End Range as day after the To date so the To date is included in the Range
        $endRange = clone $dateTo;
        $endRange->add(new \DateInterval('P1D'));

        // Create Range of Dates
        $range = new \DatePeriod($dateFrom, new \DateInterval('P1D'), $endRange);

        $roomTypesArray = [];
        foreach ($roomTypes as $roomType) {
            $roomTypesArray[$roomType->getType()] = $roomType;
        }

        // Generate list of all Days and Rooms in the range
        $missingDayRooms = [];
        foreach ($range as $day) {
            $missingDayRooms[$day->format('Y-m-d')] = $roomTypesArray;
        }

        // Eliminate the Days and Rooms that exist in the calendar
        foreach ($calendar as $dayRoom) {
            $day = $dayRoom->getDay()->format('Y-m-d');
            $type = $dayRoom->getRoomType()->getType();

            // Remove the Room Type if it already exists for this Day and Type
            if (isset($missingDayRooms[$day][$type])) {
                unset($missingDayRooms[$day][$type]);
            }

            // Remove the Date if it's now empty
            if (empty($missingDayRooms[$day])) {
                unset($missingDayRooms[$day]);
            }
        }

        return $missingDayRooms;
    }
}
