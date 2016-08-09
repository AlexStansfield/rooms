<?php

namespace spec\ApiBundle\Helper;

use ApiBundle\Helper\DateHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class DateHelperSpec
 * @package spec\ApiBundle\Helper
 * @mixin DateHelper
 */
class DateHelperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DateHelper::class);
    }

    function it_can_create_a_date_from_string()
    {
        $expected = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-08-01 00:00:00');

        $this->createDate('2016-08-01')->shouldBeLike($expected);
    }

    function it_can_create_a_date_range()
    {
        $dateOne = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-08-01 00:00:00');
        $dateTwo = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-08-02 00:00:00');
        $dateThree = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-08-03 00:00:00');
        $dateFour = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-08-04 00:00:00');

        $this->createDateRange($dateOne, $dateFour)->shouldBeLike([$dateOne, $dateTwo, $dateThree, $dateFour]);
    }
}
