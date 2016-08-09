<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CalendarController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:Calendar:index.html.twig');
    }
}
