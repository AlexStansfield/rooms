<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Calendar;
use ApiBundle\Model\CalendarViewModel;
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
     * @return CalendarViewModel|JsonResponse
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

        // Create Date Objects
        $dateFrom = $this->get('api.helper.date')->createDate($from);
        $dateTo = $this->get('api.helper.date')->createDate($to);

        // Find Existing Calendar Entries
        $calendar = $this->get('api.service.calendar')->getCalendarByDateRange($dateFrom, $dateTo);

        // Find the Missing Day Rooms
        $roomTypes = $this->get('api.repository.room_type')->findAll();
        $missingDayRooms = $this->get('api.service.calendar')
            ->determineMissingDayRooms($calendar, $roomTypes, $dateFrom, $dateTo);

        // Add missing day rooms to Calendar
        $calendar = $this->get('api.service.calendar')->addMissingDayRooms($calendar, $missingDayRooms);

        return new CalendarViewModel($dateFrom, $dateTo, $calendar);
    }

    /**
     * @Rest\View(statusCode=200)
     * @param Request $request
     * @return Calendar[]|JsonResponse
     */
    public function bulkUpdateAction(Request $request)
    {
        $requestData = $request->request->all();

        // Validate Request
        if (!$this->get('api.validator.bulk_update')->isValid($requestData)) {
            $response = [
                'code' => 400,
                'message' => 'Invalid values',
                'errors' => $this->get('api.validator.bulk_update')->getErrors()
            ];
            return new JsonResponse($response, 400);
        }

        // Get the RoomType and Dates
        $roomType = $this->get('api.repository.room_type')->findOneByType($requestData['room_type']);
        $dateFrom = $this->get('api.helper.date')->createDate($requestData['date_from']);
        $dateTo = $this->get('api.helper.date')->createDate($requestData['date_to']);

        // Bulk Update
        return $this->get('api.service.calendar')->bulkUpdate(
            $roomType,
            $dateFrom,
            $dateTo,
            isset($requestData['price']) ? $requestData['price'] : null,
            isset($requestData['availability']) ? $requestData['availability'] : null,
            isset($requestData['day_refine']) ? $requestData['day_refine'] : null
        );
    }

    /**
     * @Rest\View(statusCode=200)
     * @param Request $request
     * @return Calendar|JsonResponse
     */
    public function updateDayRoomPriceAction(Request $request)
    {
        $requestData = $request->request->all();

        // Validate Request
        if (!$this->get('api.validator.update_day_room_price')->isValid($requestData)) {
            $response = [
                'code' => 400,
                'message' => 'Invalid values',
                'errors' => $this->get('api.validator.update_day_room_price')->getErrors()
            ];
            return new JsonResponse($response, 400);
        }

        // Get the RoomType and Date
        $roomType = $this->get('api.repository.room_type')->findOneByType($requestData['room_type']);
        $date = $this->get('api.helper.date')->createDate($requestData['date']);

        // Update the Price
        $calendar = $this->get('api.service.calendar')
            ->updatePriceByRoomTypeAndDate($roomType, $date, $requestData['price']);

        // Return Calendar Entry
        return $calendar;
    }

    /**
     * @Rest\View(statusCode=200)
     * @param Request $request
     * @return Calendar|JsonResponse
     */
    public function updateDayRoomAvailabilityAction(Request $request)
    {
        $requestData = $request->request->all();

        // Validate Request
        if (!$this->get('api.validator.update_day_room_availability')->isValid($requestData)) {
            $response = [
                'code' => 400,
                'message' => 'Invalid values',
                'errors' => $this->get('api.validator.update_day_room_availability')->getErrors()
            ];
            return new JsonResponse($response, 400);
        }

        // Get the RoomType and Date
        $roomType = $this->get('api.repository.room_type')->findOneByType($requestData['room_type']);
        $date = $this->get('api.helper.date')->createDate($requestData['date']);

        // Update the Availability
        $calendar = $this->get('api.service.calendar')
            ->updateAvailabilityByRoomTypeAndDate($roomType, $date, $requestData['availability']);

        // Return Calendar Entry
        return $calendar;
    }
}
