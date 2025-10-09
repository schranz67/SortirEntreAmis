<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Place;
use App\Form\AddEditEventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        # Récupération des 3 derniers évènements
        $events = $eventRepository->findBy([], ['start' => 'ASC'], );
        return $this->render('events/list_events.html.twig', ['events' => $events]);
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
        // Récupération de l'ID précédent si existant
        $idPrec=-1;
        if ($eventIndex>0){
            $idPrec = $events[$eventIndex-1]->getId();
        }
        // Récupération de l'ID précédent si existant
        $idSuiv=-1;
        if ($eventIndex<count($events)-1){
            $idSuiv = $events[$eventIndex+1]->getId();
        }
        return $this->render('events/detail_event.html.twig', ['eventIndex' => $eventIndex, 'idPrec' => $idPrec, 'id' => $id, 'idSuiv' => $idSuiv,'events' => $events]);
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

}
