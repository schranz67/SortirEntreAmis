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
        $events = $eventRepository->findBy([], [], 3);
        var_dump($events);
        return $this->render('default/home.html.twig');
    }

    #[Route('/404', name: 'app_error_404')]
    public function error404(): Response
    {
        // On lance une exception 404
        throw $this->createNotFoundException('Page non trouvée');
    }

}
