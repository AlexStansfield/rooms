<?php

namespace ApiBundle\Model;

use ApiBundle\Entity\Calendar;
use JMS\Serializer\Annotation as JMS;

/**
 * Class CalendarViewModel
 * @package ApiBundle\Model
 */
class CalendarViewModel
{
    /**
     * @var \DateTime
     * @JMS\Type("DateTime<'Y-m-d'>")
     */
    private $from;

    /**
     * @var \DateTime
     * @JMS\Type("DateTime<'Y-m-d'>")
     */
    private $to;

    /**
     * @var array
     */
    private $calendar;

    /**
     * CalendarViewModel constructor.
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @param Calendar[] $calendar
     */
    public function __construct(\DateTime $from, \DateTime $to, $calendar)
    {
        // Add the From and To Dates
        $this->from = $from;
        $this->to = $to;

        // Create the Calendar Entries
        $this->calendar = [];
        foreach ($calendar as $calendarEntry) {
            $dateString = $calendarEntry->getDay()->format('Y-m-d');

            if (!array_key_exists($dateString, $this->calendar)) {
                $this->calendar[$dateString] = [];
            }

            $this->calendar[$dateString][$calendarEntry->getRoomTypeString()] = new CalendarDayViewModel(
                $calendarEntry->getPrice(),
                $calendarEntry->getAvailability()
            );

            ksort($this->calendar);
        }
    }
}
