<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Template;

class DefaultController extends AbstractController
{
    /**
     * Page accueil
     * @return Template
     */
    #[Route('/', name: 'default_home', methods: ['GET'])]
    public function home(EventRepository $eventRepository)
    {
        # Récupération des 3 derniers évènements
        setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
        $events = $eventRepository->findBy([], ['start' => 'ASC'], 3);

        # Récupération des inscriptions liées
        $regisrations=[];
        foreach ($events as $event) {
            $users = $event->getUser();
            foreach ($users as $user) {
                $regisrations[$event->getId()][] = $user->getId();
            }
        }

        return $this->render('default/home.html.twig', ['events' => $events, 'registrations' => $regisrations]);
    }

}
