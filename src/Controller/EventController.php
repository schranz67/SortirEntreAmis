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
    #[Route('/list', name: 'events_list', methods: ['GET'])]
    public function list()
    {
        return $this->render('events/list.html.twig');
    }

    /**
     * Page detail
     * @return Template
     */
    #[Route('/detail', name: 'events_detail', methods: ['GET'])]
    public function detail()
    {
        return $this->render('events/detail.html.twig', ['id' => 1]);
    }

    /**
     * Page detail
     * @return Template
     */
    #[Route('/detail/{id}', name: 'events_detail_id', methods: ['GET'])]
    public function detail_id($id)
    {
        return $this->render('events/detail.html.twig', ['id' => $id]);
    }
}
