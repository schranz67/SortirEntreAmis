<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Template;

class EventController extends AbstractController
{
    /**
     * Page liste
     * @return Template
     */
    #[Route('/list_events', name: 'events_list', methods: ['GET'])]
    public function list()
    {
        return $this->render('events/list_events.html.twig');
    }

    /**
     * Page detail
     * @return Template
     */
    #[Route('/detail_event', name: 'events_detail', methods: ['GET'])]
    public function detail()
    {
        return $this->render('events/detail_event.html.twig', ['id' => 1]);
    }

    /**
     * Page detail
     * @return Template
     */
    #[Route('/detail_event/{id}', name: 'events_detail_id', methods: ['GET'])]
    public function detail_id($id)
    {
        return $this->render('events/detail_event.html.twig', ['id' => $id]);
    }

    /**
     * Page création
     * @return Template
     */
    #[Route('/create_event', name: 'event_create', methods: ['GET'])]
    public function create_event()
    {
        return $this->render('events/create_event.html.twig');
    }
}
