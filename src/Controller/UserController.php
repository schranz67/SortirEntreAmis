<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Template;

class UserController extends AbstractController
{
    /**
     * Page de gestion des rôles
     *
     * @Param UserRepository $userRepository
     *
     * @return Template
     */
    #[Route('/admin/role', name: 'users_role', methods: ['GET'])]
    public function role(UserRepository $userRepository)
    {
        # Récupération de tous les utilisateurs
        $users = $userRepository->findBy([], ['name' => 'ASC', 'firstname' => 'ASC']);
        $users = array_filter($users, fn($user) => !in_array('ROLE_ADMIN', $user->getRoles()));

        return $this->render('users/handle_role.html.twig', ['users' => $users]);
    }

    /**
     * Changement de rôles
     *
     * @Param int $id
     * @Param UserRepository $userRepository
     * @Param EntityManagerInterface $entityManager
     *
     * @return Template
     */
    #[Route('/admin/change_role/{id}', name: 'users_change_role', methods: ['GET'])]
    public function change_role($id, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        # Récupération de l'utilisateur
        $user = $userRepository->find($id);

        # Ajout du rôle d'administrateur
        $user->setRoles(['ROLE_ADMIN']);

        # Validation du changement de rôle en base de données
        $entityManager->persist($user);
        $entityManager->flush();

        # Ajout du message flash de confirmation
        $this->addFlash('success', $user->getName().' '.$user->getFirstname().' est désormais administrateur !');

        # Redirection vers la page de gestion des rôles
        return $this->redirectToRoute('users_role');
    }
}
