<?php

namespace spec\ApiBundle\Manager;

use ApiBundle\Entity\Calendar;
use ApiBundle\Entity\RoomType;
use ApiBundle\Manager\CalendarManager;
use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;

/**
 * Class CalendarManagerSpec
 * @package spec\ApiBundle\Manager
 * @mixin CalendarManager
 */
class CalendarManagerSpec extends ObjectBehavior
{
    function let(EntityManager $em)
    {
        $this->beConstructedWith($em);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CalendarManager::class);
    }

    function it_will_create_calendar_with_default_price_and_availability()
    {
        $price = 1234;
        $availability = 4321;
        $date = \DateTime::createFromFormat('Y-m-d', '2016-08-01');

        $roomType = new RoomType();
        $roomType->setDefaultPrice($price);
        $roomType->setDefaultAvailability($availability);

        $expected = new Calendar();
        $expected->setRoomType($roomType);
        $expected->setDay($date);
        $expected->setAvailability($availability);
        $expected->setPrice($price);

        $this->createCalendar($roomType, $date)->shouldBeLike($expected);
    }

    function it_will_create_calendar_with_default_price()
    {
        $price = 1234;
        $availability = 4321;
        $date = \DateTime::createFromFormat('Y-m-d', '2016-08-01');

        $roomType = new RoomType();
        $roomType->setDefaultPrice($price);
        $roomType->setDefaultAvailability($availability * 2);

        $expected = new Calendar();
        $expected->setRoomType($roomType);
        $expected->setDay($date);
        $expected->setAvailability($availability);
        $expected->setPrice($price);

        $this->createCalendar($roomType, $date, $availability)->shouldBeLike($expected);
    }

    function it_will_create_calendar_with_default_availability()
    {
        $price = 1234;
        $availability = 4321;
        $date = \DateTime::createFromFormat('Y-m-d', '2016-08-01');

        $roomType = new RoomType();
        $roomType->setDefaultPrice($price * 2);
        $roomType->setDefaultAvailability($availability);

        $expected = new Calendar();
        $expected->setRoomType($roomType);
        $expected->setDay($date);
        $expected->setAvailability($availability);
        $expected->setPrice($price);

        $this->createCalendar($roomType, $date, null, $price)->shouldBeLike($expected);
    }

    function it_will_create_calendar_without_defaults()
    {
        $price = 1234;
        $availability = 4321;
        $date = \DateTime::createFromFormat('Y-m-d', '2016-08-01');

        $roomType = new RoomType();
        $roomType->setDefaultPrice($price * 2);
        $roomType->setDefaultAvailability($availability * 2);

        $expected = new Calendar();
        $expected->setRoomType($roomType);
        $expected->setDay($date);
        $expected->setAvailability($availability);
        $expected->setPrice($price);

        $this->createCalendar($roomType, $date, $availability, $price)->shouldBeLike($expected);
    }

    function it_can_save_a_calendar_entry(EntityManager $em, Calendar $calendar)
    {
        $em->persist($calendar)->shouldBeCalledTimes(1);
        $em->flush($calendar)->shouldBeCalledTimes(1);

        $this->save($calendar);
    }

    function it_can_bulk_save_multiple_calendar_entries(
        EntityManager $em,
        Calendar $calendar1,
        Calendar $calendar2,
        Calendar $calendar3,
        Calendar $calendar4
    ) {
        $calendar = [$calendar1, $calendar2, $calendar3, $calendar4];

        $em->persist($calendar1)->shouldBeCalledTimes(1);
        $em->persist($calendar2)->shouldBeCalledTimes(1);
        $em->persist($calendar3)->shouldBeCalledTimes(1);
        $em->persist($calendar4)->shouldBeCalledTimes(1);
        $em->flush()->shouldBeCalledTimes(1);

        $this->bulkSave($calendar);
    }
}
