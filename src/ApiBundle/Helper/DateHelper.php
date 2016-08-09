<?php

namespace ApiBundle\Helper;

class DateHelper
{
    const DAY_REFINE_ALL        = 1;
    const DAY_REFINE_WEEKDAYS   = 2;
    const DAY_REFINE_WEEKENDS   = 3;
    const DAY_REFINE_MONDAYS    = 4;
    const DAY_REFINE_TUESDAYS   = 5;
    const DAY_REFINE_WEDNESDAYS = 6;
    const DAY_REFINE_THURSDAYS  = 7;
    const DAY_REFINE_FRIDAYS    = 8;
    const DAY_REFINE_SATURDAYS  = 9;
    const DAY_REFINE_SUNDAYS    = 10;

    public static $refineDays = [
        self::DAY_REFINE_ALL => [1, 2, 3, 4, 5, 6, 7],
        self::DAY_REFINE_WEEKDAYS => [1, 2, 3, 4, 5],
        self::DAY_REFINE_WEEKENDS => [6, 7],
        self::DAY_REFINE_MONDAYS => [1],
        self::DAY_REFINE_TUESDAYS => [2],
        self::DAY_REFINE_WEDNESDAYS => [3],
        self::DAY_REFINE_THURSDAYS => [4],
        self::DAY_REFINE_FRIDAYS => [5],
        self::DAY_REFINE_SATURDAYS => [6],
        self::DAY_REFINE_SUNDAYS => [7]
    ];

    /**
     * Creates a valid Date (Time of Midnight) from a string
     *
     * @param string $dateString
     * @return \DateTime
     */
    public function createDate($dateString)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $dateString);
        $date->setTime(0, 0, 0);

        return $date;
    }

    /**
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @return \DateTime[]
     */
    public function createDateRange(\DateTime $dateFrom, \DateTime $dateTo)
    {
        // Create End Range as day after the To date so the To date is included in the Range
        $endRange = clone $dateTo;
        $endRange->add(new \DateInterval('P1D'));

        $range = [];
        $period = new \DatePeriod($dateFrom, new \DateInterval('P1D'), $endRange);

        foreach ($period as $date) {
            $range[] = $date;
        }

        // Create Range of Dates
        return $range;
    }

    /**
     * Refine a range of dates and return only those that match the refine criteria
     *
     * @param \DateTime[] $range
     * @param int $refine
     * @return \DateTime[]
     */
    public function refineDateRange($range, $refine)
    {
        $refined = [];

        // If they supplied invalid refine then return empty array
        if (! array_key_exists($refine, self::$refineDays)) {
            return $refined;
        }

        // Loop through the range and keep the dates that match
        foreach ($range as $date) {
            $day = (int) $date->format('N');

            if (in_array($day, self::$refineDays[$refine], true)) {
                $refined[] = $date;
            }
        }

        return $refined;
    }
}
