<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Form\AddEditEventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Template;

class EventController extends AbstractController
{
    /**
     * Page liste
     * @return Template
     */
    #[Route('/profile/list_events', name: 'events_list', methods: ['GET'])]
    public function list(EventRepository $eventRepository)
    {
        # Récupération de tous les évènements
        $events = $eventRepository->findBy([], ['start' => 'ASC'], );

        # Récupération des inscriptions liées
        $regisrations=[];
        foreach ($events as $event) {
            $users = $event->getUser();
            foreach ($users as $user) {
                $regisrations[$event->getId()][] = $user->getId();
            }
        }

        return $this->render('events/list_events.html.twig', ['events' => $events, 'registrations' => $regisrations,]);
    }

    /**
     * Page detail
     * @return Template
     */
    #[Route('/profile/detail_event/{id}', name: 'events_detail_id', methods: ['GET'])]
    public function detail_id($id, EventRepository $eventRepository)          // public function detail_id($id)
    {
        // Récupération de tous les évènements
        $events = $eventRepository->findBy([], ['start' => 'ASC'], );
        // Recherche de la ligne où se trouve l'évènement
        $eventIndex = 0;
        foreach ($events as $index => $event) {
            if ($event->getId() === (int) $id) {
                $eventIndex = $index;
                break;
            }
        }
        // Comptage du nombre d'inscrits
        $nbRegs[]=[0,0,0];
        $nbRegs[1]=$events[$eventIndex]->getUser()->count();
        // Récupération de l'ID précédent si existant
        $idPrec=-1;
        if ($eventIndex>0){
            $idPrec = $events[$eventIndex-1]->getId();
            $nbRegs[0]=$events[$eventIndex-1]->getUser()->count();
        }
        // Récupération de l'ID précédent si existant
        $idSuiv=-1;
        if ($eventIndex<count($events)-1){
            $idSuiv = $events[$eventIndex+1]->getId();
            $nbRegs[2]=$events[$eventIndex+1]->getUser()->count();
        }
        return $this->render('events/detail_event.html.twig', ['eventIndex' => $eventIndex, 'nbRegs' => $nbRegs, 'idPrec' => $idPrec, 'id' => $id, 'idSuiv' => $idSuiv,'events' => $events]);
    }

    /**
     * Page création
     * @return Template
     */
    #[Route('/admin/add_edit_event', name: 'event_create')]
    public function create_event(Request $request, EntityManagerInterface $entityManager,  SluggerInterface $slugger)
    {
        // Préparation du nouvel objet Event
        $event = new Event();
        $event->setOrganizer($this->getUser());

        // Création du formulaire
        $form = $this->createForm(AddEditEventFormType::class, $event);
        $form->handleRequest($request);
        $texts['titre'] = "Création";
        $texts['verb'] = "Créer";

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire);
            $event = $form->getData();
            // Récupération de l'image
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/events_images';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $slugger->slug($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                // Déplacement du fichier vers le dossier défini
                $uploadedFile->move($destination, $newFilename);
                // Sauvegarde du nom du fichier
                $event->setImage($newFilename);
            }

            // Envoi du nouvel évènement en base de données
            $entityManager->persist($event);
            $entityManager->flush();

            // Redirection vers le détail de l'évènement
            return $this->redirectToRoute('events_detail_id', ['id' => $event->getId(),]);
        }

        // Renvoi vers le formulaire
        return $this->render('events/add_edit_event.html.twig', ['form' => $form, 'texts' => $texts]);
    }

    /**
     * Page d'édition
     * @return Template
     */
    #[Route('/admin/add_edit_event/{id}', name: 'event_add')]
    public function edit_event($id, EventRepository $eventRepository, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        // Récupération de l'évènement
        $event = $eventRepository->find($id);

        // Création du formulaire
        $form = $this->createForm(AddEditEventFormType::class, $event);
        $form->handleRequest($request);
        $texts['titre'] = "Modification";
        $texts['verb'] = "Modifier";

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $event = $form->getData();
            // Récupération de l'image
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/events_images';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $slugger->slug($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                // Déplacement du fichier vers le dossier défini
                $uploadedFile->move($destination, $newFilename);
                // Sauvegarde du nom du fichier
                $event->setImage($newFilename);
            }

            // Envoi de l'évènement modifié en base de données
            $entityManager->persist($event);
            $entityManager->flush();

            // Redirection vers le détail de l'évènement
            return $this->redirectToRoute('events_detail_id', ['id' => $event->getId(),]);
        }

        // Renvoi vers le formulaire
        return $this->render('events/add_edit_event.html.twig', ['form' => $form, 'texts' => $texts]);
    }


    /**
     * Suppression de l'évènement
     * @return Template
     */
    #[Route('/admin/delete_event/{id}', name: 'event_delete')]
    public function delete($id, EventRepository $eventRepository, EntityManagerInterface $entityManager)
    {
        // Récupération de l'évènement
        $event = $eventRepository->find($id);

        // Suppression de l'évènement
        if ($event) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        // Redirection vers la liste des évènements
        return $this->redirectToRoute('events_list');
    }

    /**
     * Inscription à l'évènement
     * @return Template
     */
    #[Route('/profile/register_event/{id}', name: 'event_register')]
    public function register_event($id, EventRepository $eventRepository, EntityManagerInterface $entityManager)
    {
        // Récupération de l'évènement
        $event = $eventRepository->find($id);

        // Liaison de l'évènement à l'utilisateur
        $event->addUser($this->getUser());

        // Ajout de l'inscription en base de données
        $entityManager->persist($event);
        $entityManager->flush();

        // Ajout du message flash
        $this->addFlash('success', 'Votre inscription a bien été prise en compte !');

        // Redirection vers la liste des évènements
        return $this->redirectToRoute('events_detail_id',['id' => $event->getId()]);
    }

    /**
     * Désinscription à l'évènement
     * @return Template
     */
    #[Route('/profile/unregister_event/{id}', name: 'event_unregister')]
    public function unregister_event($id, EventRepository $eventRepository, EntityManagerInterface $entityManager)
    {
        // Récupération de l'évènement
        $event = $eventRepository->find($id);

        // Liaison de l'évènement à l'utilisateur
        $user = $this->getUser();
        $event->removeUser($user);

        // Suppression de l'inscription en base de données
        $entityManager->persist($event);
        $entityManager->flush();

        // Redirection vers la liste des évènements
        return $this->redirectToRoute('events_list');
    }
}
