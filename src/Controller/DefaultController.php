<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Service\EventManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    /**
     * Page accueil
     *
     * @param EventRepository $eventRepository
     * @param EventManager $eventManager
 *
     * @return Response
     */
    #[Route('/', name: 'default_home', methods: ['GET'])]
    public function home(EventRepository $eventRepository, EventManager $eventManager)
    {
        # Récupération des 3 prochains évènements à venir
        $events = $eventRepository->findUpcomingEvents(3);

        # Récupération des inscriptions liées aux évènements
        $registrations=$eventManager->getRegistrationEvents($events);

        # Rendu du template Twig avec les événements et les inscriptions
        return $this->render('default/home.html.twig', ['events' => $events, 'registrations' => $registrations, 'view' => 'home']);
    }

}
