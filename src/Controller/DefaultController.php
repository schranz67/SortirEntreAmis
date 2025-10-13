<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Service\EventManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Template;

class DefaultController extends AbstractController
{
    /**
     * Page accueil
     *
     * @param EventRepository $eventRepository
     * @param EventManager $eventManager
 *
     * @return Template
     */
    #[Route('/', name: 'default_home', methods: ['GET'])]
    public function home(EventRepository $eventRepository, EventManager $eventManager)
    {
        # Récupération des 3 derniers évènements
        $events = $eventRepository->findBy([], ['start' => 'ASC'], 3);

        # Récupération des inscriptions liées aux évènements
        $registrations=$eventManager->getRegistrationEvents($events);

        # Rendu du template Twig avec les événements et les inscriptions
        return $this->render('default/home.html.twig', ['events' => $events, 'registrations' => $registrations, 'view' => 'home']);
    }

}
