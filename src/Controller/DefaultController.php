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

}
