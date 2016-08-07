<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Calendar;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CalendarController
 * @package ApiBundle\Controller
 */
class CalendarController extends FOSRestController
{
    /**
     * @Rest\View(statusCode=200)
     * @param string $from
     * @param string $to
     * @return Calendar[]|JsonResponse
     */
    public function getByDateRangeAction($from, $to)
    {
        // Validate date range values
        if (!$this->get('api.validator.date_range')->isValid(['date_from' => $from, 'date_to' => $to])) {
            $response = [
                'code' => 400,
                'message' => 'Invalid values',
                'errors' => $this->get('api.validator.date_range')->getErrors()
            ];
            return new JsonResponse($response, 400);
        }

        $dateFrom = \DateTime::createFromFormat('Y-m-d', $from);
        $dateTo = \DateTime::createFromFormat('Y-m-d', $to);

        // Find Existing Calendar Entries
        $calendar = $this->get('api.service.calendar')->getCalendarByDateRange($dateFrom, $dateTo);

        // Find the Missing Day Rooms
        $roomTypes = $this->get('api.repository.room_type')->findAll();
        $missingDayRooms = $this->get('api.service.calendar')
            ->determineMissingDayRooms($calendar, $roomTypes, $dateFrom, $dateTo);

        // Add missing day rooms to Calendar
        $calendar = $this->get('api.service.calendar')->addMissingDayRooms($calendar, $missingDayRooms);

        return $calendar;
    }

    /**
     * @Rest\View(statusCode=200)
     * @param Request $request
     * @return Calendar[]
     */
    public function bulkUpdateRoomsAction(Request $request)
    {
    }

    /**
     * @Rest\View(statusCode=200)
     * @param Request $request
     * @return Calendar[]
     */
    public function updateRoom(Request $request)
    {

    }
}
