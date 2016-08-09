<?php

namespace ApiBundle\Model;

/**
 * Class CalendarDayViewModel
 * @package ApiBundle\Model
 */
class CalendarDayViewModel
{
    /**
     * @var int
     */
    private $price;

    /**
     * @var float
     */
    private $availability;

    /**
     * CalendarDayViewModel constructor.
     *
     * @param int $price
     * @param float $availability
     */
    public function __construct($price, $availability)
    {
        $this->price = $price;
        $this->availability = $availability;
    }
}
