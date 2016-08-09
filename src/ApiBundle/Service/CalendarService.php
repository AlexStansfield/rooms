<?php

namespace ApiBundle\Service;

use ApiBundle\Entity\Calendar;
use ApiBundle\Entity\RoomType;
use ApiBundle\Helper\DateHelper;
use ApiBundle\Manager\CalendarManager;
use ApiBundle\Repository\CalendarRepository;

/**
 * Class CalendarService
 * @package ApiBundle\Service
 */
class CalendarService
{
    /** @var CalendarManager */
    private $calendarManager;

    /** @var CalendarRepository */
    private $calendarRepo;

    /** @var DateHelper */
    private $dateHelper;

    /**
     * CalendarService constructor
     *
     * @param CalendarManager $calendarManager
     * @param CalendarRepository $calendarRepo
     * @param DateHelper $dateHelper
     */
    public function __construct(
        CalendarManager $calendarManager,
        CalendarRepository $calendarRepo,
        DateHelper $dateHelper
    ) {
        $this->calendarManager = $calendarManager;
        $this->calendarRepo = $calendarRepo;
        $this->dateHelper = $dateHelper;
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
                $calendar[] = $this->calendarManager->createCalendar($dayRoomType, $dayDate);
            }
        }

        // Save any new entries
        $this->calendarManager->bulkSave($calendar);

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
        // Create date range
        $range = $this->dateHelper->createDateRange($dateFrom, $dateTo);

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
