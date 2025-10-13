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
     * Page de liste des évènements
     *
     * @param EventRepository $eventRepository
     *
     * @return Template
     */
    #[Route('/profile/list_events', name: 'events_list', methods: ['GET'])]
    public function list(EventRepository $eventRepository)
    {
        # Récupération de tous les évènements
        $events = $eventRepository->findBy([], ['start' => 'ASC']);

        # Récupération des inscriptions liées à chaque évènement
        $registrations=[];
        foreach ($events as $event) {
            $registrations[$event->getId()] = $event->getRegistrations();
        }

        # Rendu du template Twig avec les événements et les inscriptions
        return $this->render('events/list_events.html.twig', ['events' => $events, 'registrations' => $registrations, 'view' => 'list']);
    }

    /**
     * Page de detail d'un évènement ainsi que le précédent et le suivant
     *
     * @param $id
     * @param EventRepository $eventRepository
     *
     * @return Template
     */
    #[Route('/profile/detail_event/{id}', name: 'events_detail_id', methods: ['GET'])]
    public function detail_id($id, EventRepository $eventRepository)          // public function detail_id($id)
    {
        # Récupération de tous les évènements
        $events = $eventRepository->findBy([], ['start' => 'ASC']);

        # Recherche de l'index du tableau où se trouve l'évènement lié à l'id passé en paramètre
        $eventIndex = 0;
        foreach ($events as $index => $event) {
            if ($event->getId() === (int) $id) {
                $eventIndex = $index;
                break;
            }
        }

        # Récupération des inscriptions liées à l'évènement courant
        $registrations[$events[$index]->getId()] = $events[$index]->getRegistrations();

        # Comptage du nombre d'inscrits à l'évènement en cours
        $nbRegs[]=[0,0,0];
        $nbRegs[1]=$events[$eventIndex]->countUsers();

        # Récupération de l'ID précédent si existant et comptage du nombre d'inscrits à l'évènement précédent
        $idPrec=-1;
        if ($eventIndex>0){
            $idPrec = $events[$eventIndex-1]->getId();
            $nbRegs[0]=$events[$eventIndex-1]->countUsers();
        }

        # Récupération de l'id suivant si existant et comptage du nombre d'inscrits à l'évènement suivant
        $idSuiv=-1;
        if ($eventIndex<count($events)-1){
            $idSuiv = $events[$eventIndex+1]->getId();
            $nbRegs[2]=$events[$eventIndex+1]->countUsers();
        }

        # Rendu du template Twig avec toutes les informations nécessaires
        return $this->render('events/detail_event.html.twig', ['eventIndex' => $eventIndex, 'nbRegs' => $nbRegs, 'idPrec' => $idPrec, 'id' => $id, 'idSuiv' => $idSuiv,'events' => $events, 'registrations' => $registrations]);
    }

    /**
     * Pages de création et d'édition d'un évènement
     *
     * @param int|null $id
     * @param EventRepository $eventRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     *
 * @return Template
     */
    #[Route('/admin/add_edit_event', name: 'event_create', methods: ['GET', 'POST'])]
    #[Route('/admin/add_edit_event/{id}', name: 'event_add', methods: ['GET', 'POST'])]
    public function create_event(?int $id = null, EventRepository $eventRepository, Request $request, EntityManagerInterface $entityManager,  SluggerInterface $slugger)
    {
        # Récupération de l'évènement si présence d'un id
        if ($id) {
            $event = $eventRepository->find($id);
            $texts['titre'] = "Modification";
            $texts['verb'] = "Modifier";
        }
        # Préparation du nouvel objet Event en affectant l'identifiant de l'organisateur si absence d'un id
        else{
            $event = new Event();
            $event->setOrganizer($this->getUser());
            $texts['titre'] = "Création";
            $texts['verb'] = "Créer";
        }

        # Création du formulaire
        $form = $this->createForm(AddEditEventFormType::class, $event);
        $form->handleRequest($request);

        # Vérification que le formulaire a été soumis et est valide pour traitement des données
        if ($form->isSubmitted() && $form->isValid()) {
            # Récupération des données du formulaire;
            $event = $form->getData();
            # Récupération de l'image
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('events_images_directory');
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $slugger->slug($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                # Déplacement du fichier vers le dossier défini
                $uploadedFile->move($destination, $newFilename);
                # Sauvegarde du nom du fichier
                $event->setImage($newFilename);
            }

            # Envoi du nouvel évènement en base de données
            $entityManager->persist($event);
            $entityManager->flush();

            # Renvoi vers le détail du nouvel évènement
            return $this->redirectToRoute('events_detail_id', ['id' => $event->getId(),]);
        }

        # Rendu du template Twig de l'édition d'un évènement
        return $this->render('events/add_edit_event.html.twig', ['form' => $form, 'texts' => $texts]);
    }

    /**
     * Suppression de l'évènement
     *
     * @param int $id
     * @param EventRepository $eventRepository
     * @param EntityManagerInterface $entityManager
     *
     * @return Template
     */
    #[Route('/admin/delete_event/{id}', name: 'event_delete', methods: ['GET'])]
    public function delete($id, EventRepository $eventRepository, EntityManagerInterface $entityManager)
    {
        # Récupération de l'évènement
        $event = $eventRepository->find($id);

        // Suppression de l'image si elle existe
        $imageFilename = $event->getImage();
        if ($imageFilename) {
            $imagePath = $this->getParameter('events_images_directory')  . '/' . $imageFilename;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        # Suppression de l'évènement
        $entityManager->remove($event);
        $entityManager->flush();

        # Redirection vers la liste des évènements
        return $this->redirectToRoute('events_list');
    }

    /**
     * Inscription à l'évènement
     *
     * @param int $id
     * @param EventRepository $eventRepository
     * @param EntityManagerInterface $entityManager
     *
     * @return Template
     */
    #[Route('/profile/register_event/{view}_{id}', name: 'event_register', methods: ['GET'])]
    public function register_event($view, $id, EventRepository $eventRepository, EntityManagerInterface $entityManager)
    {
        # Récupération de l'évènement
        $event = $eventRepository->find($id);

        # Liaison de l'évènement à l'utilisateur
        $event->addUser($this->getUser());

        # Ajout de l'inscription en base de données
        $entityManager->persist($event);
        $entityManager->flush();

        # Ajout du message flash de confirmation
        if ($view === 'detail') {
            $this->addFlash('success', 'Votre inscription a bien été prise en compte !');
        }

        # Redirection vers la page source
        switch ($view){
            case 'list': return $this->redirectToRoute('events_list');
            case 'detail': return $this->redirectToRoute('events_detail_id',['id' => $event->getId()]);
            default: return $this->redirectToRoute('default_home');
        }
    }

    /**
     * Désinscription à l'évènement
     *
     * @param int $id
     * @param EventRepository $eventRepository
     * @param EntityManagerInterface $entityManager
     *
     * @return Template
     */
    #[Route('/profile/unregister_event/{view}_{id}', name: 'event_unregister', methods: ['GET'])]
    public function unregister_event($view, $id, EventRepository $eventRepository, EntityManagerInterface $entityManager)
    {
        # Récupération de l'évènement
        $event = $eventRepository->find($id);

        # Liaison de l'évènement à l'utilisateur
        $user = $this->getUser();
        $event->removeUser($user);

        # Suppression de l'inscription en base de données
        $entityManager->persist($event);
        $entityManager->flush();

        # Ajout du message flash de confirmation
        if ($view === 'detail') {
            $this->addFlash('success', 'Votre désinscription a bien été prise en compte !');
        }

        # Redirection vers la page source
        switch ($view){
            case 'list': return $this->redirectToRoute('events_list');
            case 'detail': return $this->redirectToRoute('events_detail_id',['id' => $event->getId()]);
            default: return $this->redirectToRoute('default_home');
        }
    }
}
