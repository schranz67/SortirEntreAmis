<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventRepository;
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
    public function list(EventRepository $eventRepository)
    {
        # Récupération des 3 derniers évènements
        $events = $eventRepository->findBy([], ['start' => 'ASC'], );
        return $this->render('events/list_events.html.twig', ['events' => $events]);
    }

    /**
     * Page detail
     * @return Template
     */
    #[Route('/detail_event/{id}', name: 'events_detail_id', methods: ['GET'])]
    public function detail_id($id, EventRepository $eventRepository)          // public function detail_id($id)
    {
        // Récupération de tous les évènements
        $events = $eventRepository->findBy([], ['start' => 'ASC'], );
        // Recherche de la ligne où se trouve l'évènement
        $eventIndex = 0;
        foreach ($events as $index => $event) {
            if ($event->getId() === (int) $id) {
                $eventIndex = $index;
                break;
            }
        }
        // Récupération de l'ID précédent si existant
        $idPrec=-1;
        if ($eventIndex>0){
            $idPrec = $events[$eventIndex-1]->getId();
        }
        // Récupération de l'ID précédent si existant
        $idSuiv=-1;
        if ($eventIndex<count($events)-1){
            $idSuiv = $events[$eventIndex+1]->getId();
        }
        return $this->render('events/detail_event.html.twig', ['eventIndex' => $eventIndex, 'idPrec' => $idPrec, 'id' => $id, 'idSuiv' => $idSuiv,'events' => $events]);
    }

    /**
     * Page création
     * @return Template
     */
    #[Route('/add_edit_event', name: 'event_create', methods: ['GET'])]
    public function create_event()
    {
        return $this->render('events/add_edit_event.html.twig', ['id' => 0]);
    }

    /**
     * Page d'édition
     * @return Template
     */
    #[Route('/add_edit_event/{id}', name: 'event_add', methods: ['GET'])]
    public function edit_event($id)
    {
        return $this->render('events/add_edit_event.html.twig', ['id' => $id]);
    }
}
