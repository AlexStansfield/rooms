<?php

namespace spec\ApiBundle\Service;

use ApiBundle\Entity\Calendar;
use ApiBundle\Entity\RoomType;
use ApiBundle\Repository\CalendarRepository;
use ApiBundle\Service\CalendarService;
use PhpSpec\ObjectBehavior;

/**
 * Class CalendarServiceSpec
 * @package spec\ApiBundle\Service
 * @mixin CalendarService
 */
class CalendarServiceSpec extends ObjectBehavior
{
    function let(CalendarRepository $calendarRepo)
    {
        $this->beConstructedWith($calendarRepo);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CalendarService::class);
    }

    function it_can_get_calendar_by_date_range(CalendarRepository $calendarRepo, Calendar $day1, Calendar $day2)
    {
        $dateFrom = \DateTime::createFromFormat('Y-m-d', '2016-07-01');
        $dateTo = \DateTime::createFromFormat('Y-m-d', '2016-08-01');

        $expected = [$day1, $day2];

        $calendarRepo->findByDateRange($dateFrom, $dateTo)->willReturn([$day1, $day2]);

        $this->getCalendarByDateRange($dateFrom, $dateTo)->shouldBe($expected);
    }

    function it_can_determine_missing_day_rooms(
        RoomType $typeOne,
        RoomType $typeTwo,
        Calendar $dayOneTypeOne,
        Calendar $dayTwoTypeTwo
    ) {
        // Create the Date Range
        $dayOne = \DateTime::createFromFormat('Y-m-d', '2016-08-01');
        $dayTwo = \DateTime::createFromFormat('Y-m-d', '2016-08-02');
        $dayThree = \DateTime::createFromFormat('Y-m-d', '2016-08-03');

        // Expected Result
        $expected = [
            '2016-08-01' => ['type_two' => $typeTwo],
            '2016-08-02' => ['type_one' => $typeOne],
            '2016-08-03' => ['type_one' => $typeOne, 'type_two' => $typeTwo]
        ];

        // Create Arrays passed into method
        $calendar = [$dayOneTypeOne, $dayTwoTypeTwo];
        $roomTypes = [$typeOne, $typeTwo];

        // Mock Room Types
        $typeOne->getType()->willReturn('type_one');
        $typeTwo->getType()->willReturn('type_two');

        // Mock Calendar Days
        $dayOneTypeOne->getRoomType()->willReturn($typeOne);
        $dayOneTypeOne->getDay()->willReturn($dayOne);
        $dayTwoTypeTwo->getRoomType()->willReturn($typeTwo);
        $dayTwoTypeTwo->getDay()->willReturn($dayTwo);

        $this->determineMissingDayRooms($calendar, $roomTypes, $dayOne, $dayThree)->shouldBe($expected);
    }

    function it_can_determine_there_are_no_missing_day_rooms_when_calendar_complete(
        RoomType $typeOne,
        RoomType $typeTwo,
        Calendar $dayOneTypeOne,
        Calendar $dayOneTypeTwo,
        Calendar $dayTwoTypeOne,
        Calendar $dayTwoTypeTwo
    ) {
        // Create the Date Range
        $dayOne = \DateTime::createFromFormat('Y-m-d', '2016-08-01');
        $dayTwo = \DateTime::createFromFormat('Y-m-d', '2016-08-02');

        // Expected Result
        $expected = [];

        // Create Arrays passed into method
        $calendar = [$dayOneTypeOne, $dayOneTypeTwo, $dayTwoTypeOne, $dayTwoTypeTwo];
        $roomTypes = [$typeOne, $typeTwo];

        // Mock Room Types
        $typeOne->getType()->willReturn('type_one');
        $typeTwo->getType()->willReturn('type_two');

        // Mock Calendar Days
        $dayOneTypeOne->getRoomType()->willReturn($typeOne);
        $dayOneTypeOne->getDay()->willReturn($dayOne);
        $dayOneTypeTwo->getRoomType()->willReturn($typeTwo);
        $dayOneTypeTwo->getDay()->willReturn($dayOne);
        $dayTwoTypeOne->getRoomType()->willReturn($typeOne);
        $dayTwoTypeOne->getDay()->willReturn($dayTwo);
        $dayTwoTypeTwo->getRoomType()->willReturn($typeTwo);
        $dayTwoTypeTwo->getDay()->willReturn($dayTwo);

        $this->determineMissingDayRooms($calendar, $roomTypes, $dayOne, $dayTwo)->shouldBe($expected);
    }

    function it_can_create_add_calendar_entries_from_missing_day_rooms(
        RoomType $typeOne,
        RoomType $typeTwo,
        Calendar $dayOneTypeOne,
        Calendar $dayTwoTypeTwo
    ) {
        // Dates
        $dayOne = \DateTime::createFromFormat('Y-m-d', '2016-08-01');
        $dayTwo = \DateTime::createFromFormat('Y-m-d', '2016-08-02');

        // Mock Room Types
        $typeOne->getDefaultAvailability()->willReturn(5);
        $typeOne->getDefaultPrice()->willReturn(1111);
        $typeTwo->getDefaultAvailability()->willReturn(10);
        $typeTwo->getDefaultPrice()->willReturn(2222);

        // Mock Calendar Days
        $dayOneTypeOne->getRoomType()->willReturn($typeOne);
        $dayOneTypeOne->getDay()->willReturn($dayOne);
        $dayTwoTypeTwo->getRoomType()->willReturn($typeTwo);
        $dayTwoTypeTwo->getDay()->willReturn($dayTwo);

        $calendar = [$dayOneTypeOne, $dayTwoTypeTwo];

        // Missing Day Rooms
        $missingDayRooms = [
            '2016-08-01' => ['type_two' => $typeTwo],
            '2016-08-02' => ['type_one' => $typeOne]
        ];

        // Create expected new Calendar Entities
        $dayOneTypeTwo = new Calendar();
        $dayOneTypeTwo->setRoomType($typeTwo);
        $dayOneTypeTwo->setDay($dayOne);
        $dayOneTypeTwo->setAvailability(10);
        $dayOneTypeTwo->setPrice(2222);
        $dayTwoTypeOne = new Calendar();
        $dayTwoTypeOne->setRoomType($typeOne);
        $dayTwoTypeOne->setDay($dayTwo);
        $dayTwoTypeOne->setAvailability(5);
        $dayTwoTypeOne->setPrice(1111);

        // Create Expected array
        $expected = array_merge($calendar, [$dayOneTypeTwo, $dayTwoTypeOne]);

        $this->addMissingDayRooms($calendar, $missingDayRooms)->shouldBeLike($expected);
    }
}
