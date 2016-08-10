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
        $today = $this->dateHelper->createDate((new \DateTime())->format('Y-m-d'));

        // Create date range
        $range = $this->dateHelper->createDateRange($dateFrom, $dateTo);

        $roomTypesArray = [];
        foreach ($roomTypes as $roomType) {
            $roomTypesArray[$roomType->getType()] = $roomType;
        }

        // Generate list of all Days and Rooms in the range
        $missingDayRooms = [];
        foreach ($range as $day) {
            if ($day >= $today) {
                $missingDayRooms[$day->format('Y-m-d')] = $roomTypesArray;
            }
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

    /**
     * Bulk Update Calendar by Room Type and Dates
     *
     * @param RoomType $roomType
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @param float|null $price
     * @param int|null $availability
     * @param int|null $dayRefine
     * @return array
     */
    public function bulkUpdate(
        RoomType $roomType,
        \DateTime $dateFrom,
        \DateTime $dateTo,
        $price = null,
        $availability = null,
        $dayRefine = null
    ) {
        $toUpdate = [];

        // Grab the Calendar
        $calendar = $this->calendarRepo->findByRoomTypeAndDateRange($roomType, $dateFrom, $dateTo);

        // Create date range and refine it
        $range = $this->dateHelper->createDateRange($dateFrom, $dateTo);

        // Refine the dates if needed
        if (null !== $dayRefine) {
            $range = $this->dateHelper->refineDateRange($range, $dayRefine);
        }

        // Determine which Calendar Entries to update
        foreach ($calendar as $calendarEntry) {
            if (in_array($calendarEntry->getDay(), $range)) {
                $toUpdate[$calendarEntry->getDay()->format('Y-m-d')] = $calendarEntry;
            }
        }

        // Create any missing calendar entries
        foreach ($range as $date) {
            if (!in_array($date->format('Y-m-d'), array_keys($toUpdate), true)) {
                $toUpdate[$date->format('Y-m-d')] = $this->calendarManager->createCalendar($roomType, $date);
            }
        }

        // Set the Price and Availability
        foreach ($toUpdate as $calendarEntry) {
            if (null !== $price) {
                $calendarEntry->setPrice($price);
            }

            if (null !== $availability) {
                $calendarEntry->setAvailability($availability);
            }
        }

        // Save them
        $this->calendarManager->bulkSave($toUpdate);

        return $toUpdate;
    }

    /**
     * @param RoomType $roomType
     * @param \DateTime $date
     * @param float $price
     * @return Calendar
     */
    public function updatePriceByRoomTypeAndDate(RoomType $roomType, \DateTime $date, $price)
    {
        // Find the Calendar Entry
        $calendar = $this->calendarRepo->findOneByRoomTypeAndDate($roomType, $date);

        // Create Calendar Entry if missing
        if (null === $calendar) {
            $calendar = $this->calendarManager->createCalendar($roomType, $date);
        }

        // Set Price
        $calendar->setPrice($price);

        // Save the Calendar
        $this->calendarManager->save($calendar);

        return $calendar;
    }

    /**
     * @param RoomType $roomType
     * @param \DateTime $date
     * @param integer $availability
     * @return Calendar
     */
    public function updateAvailabilityByRoomTypeAndDate(RoomType $roomType, \DateTime $date, $availability)
    {
        // Find the Calendar Entry
        $calendar = $this->calendarRepo->findOneByRoomTypeAndDate($roomType, $date);

        // Create Calendar Entry if missing
        if (null === $calendar) {
            $calendar = $this->calendarManager->createCalendar($roomType, $date);
        }

        // Set Availability
        $calendar->setAvailability($availability);

        // Save the Calendar
        $this->calendarManager->save($calendar);

        return $calendar;
    }
}
