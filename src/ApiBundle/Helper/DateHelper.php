<?php

namespace ApiBundle\Helper;

class DateHelper
{
    const DAY_REFINE_MONDAYS    = 1;
    const DAY_REFINE_TUESDAYS   = 2;
    const DAY_REFINE_WEDNESDAYS = 4;
    const DAY_REFINE_THURSDAYS  = 8;
    const DAY_REFINE_FRIDAYS    = 16;
    const DAY_REFINE_SATURDAYS  = 32;
    const DAY_REFINE_SUNDAYS    = 64;

    private static $refineMap = [
        1 => self::DAY_REFINE_MONDAYS,
        2 => self::DAY_REFINE_TUESDAYS,
        3 => self::DAY_REFINE_WEDNESDAYS,
        4 => self::DAY_REFINE_THURSDAYS,
        5 => self::DAY_REFINE_FRIDAYS,
        6 => self::DAY_REFINE_SATURDAYS,
        7 => self::DAY_REFINE_SUNDAYS
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

        // Loop through the range and keep the dates that match
        foreach ($range as $date) {
            $day = (int) $date->format('N');

            $dayBitwise = self::$refineMap[$day];

            if ($dayBitwise === ($dayBitwise & $refine)) {
                $refined[] = $date;
            }
        }

        return $refined;
    }
}
