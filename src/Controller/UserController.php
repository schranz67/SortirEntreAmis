<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Template;

class UserController extends AbstractController
{
    /**
     * Page rôle
     * @return Template
     */
    #[Route('/admin/role', name: 'users_role', methods: ['GET'])]
    public function role()
    {
        return $this->render('users/handle_role.html.twig');
    }
}
