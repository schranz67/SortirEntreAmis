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
    #[Route('/role', name: 'users_role', methods: ['GET'])]
    public function role()
    {
        return $this->render('users/handle_role.html.twig');
    }

    /**
     * Page rôle
     * @return Template
     */
    #[Route('/create_account', name: 'users_create_account', methods: ['GET'])]
    public function create_account()
    {
        return $this->render('users/create_account.html.twig');
    }

    /**
     * Page rôle
     * @return Template
     */
    #[Route('/login', name: 'users_login', methods: ['GET'])]
    public function login()
    {
        return $this->render('users/login.html.twig');
    }
}
