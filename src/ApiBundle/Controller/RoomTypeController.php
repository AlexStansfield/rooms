<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\RoomType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class CalendarController
 * @package ApiBundle\Controller
 */
class RoomTypeController extends FOSRestController
{
    /**
     * @Rest\View(statusCode=200)
     * @return RoomType[]
     */
    public function getAllAction()
    {
        return $this->get('api.repository.room_type')->findAll();
    }
}
