<?php

namespace App\Controller;

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
    public function home()
    {
        return $this->render('default/home.html.twig');
    }

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

    /**
     * Page rôle
     * @return Template
     */
    #[Route('/role', name: 'users_role', methods: ['GET'])]
    public function role()
    {
        return $this->render('users/handle_role.html.twig');
    }


    /**
     * Page catégorie des évènements
     * ex. https://localhost:8000/categorie/sport
     * ex. https://localhost:8000/categorie/spectacle
     * @return Response
     */
    #[Route('/categorie/{type}', name: 'default_category', methods: ['GET'])]
    public function category($type)
    {
        # return new Response("<h1>Catégorie : $type</h1>");
        return $this->render('default/category.html.twig', ['type' => $type]);
    }

    /**
     * Page pour afficher un évènement
     * ex. https://localhost:8000/categorie/spectacle/week-end-raclette-a-baggersee_874456
     * ex. https://localhost:8000/categorie/{param:category}/{param_titre}_{param_id}
     * @return Response
     */
    #[Route('/{category}/{title}_{id}', name: 'default_event', methods: ['GET'])]
    public function event($category, $title, $id)
    {
        return new Response("
            <h1>Catégorie : $category
                <br> Titre $title,
                <br> ID $id.
            </h1>");
    }

}
