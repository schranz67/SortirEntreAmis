<?php

namespace App\Tests\Controller;

use App\Controller\EventController;
use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\User;
use App\Service\FileUploadManager;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class EventControllerAddEditUnitTest extends TestCase
{
    private $eventRepository;
    private $entityManager;
    private $fileUploadManager;
    private $controller;
    private $form;
    private $client;

    protected function setUp(): void
    {
        // Création de mocks pour toutes les dépendances du contrôleur.
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->fileUploadManager = $this->createMock(FileUploadManager::class);
        $this->form = $this->createMock(FormInterface::class);

        // Création du mock du contrôleur
        $this->controller = $this->getMockBuilder(EventController::class)
            ->onlyMethods(['getUser', 'createForm', 'render', 'redirectToRoute', 'getParameter'])->getMock();

        // Création d'un utilisateur non admin
        $this->user = new User();
        $this->user->setRoles(['ROLE_USER']);

        // Configuration du comportement de getUser()
        $this->controller->method('getUser')->willReturn($this->user);
    }

    public function testAddNewEventRendersTemplate(): void
    {
        $user = new User();
        $this->controller->method('getUser')->willReturn($user);
        $this->controller->method('createForm')->willReturn($this->form);

        $this->form->method('isSubmitted')->willReturn(false);

        // On prépare un tableau pour capturer les paramètres passés à render
        $capturedParams = null;
        $this->controller->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($template, $params) use (&$capturedParams) {
                $capturedParams = $params;
                return new Response();
            });

        $request = new Request();
        $response = $this->controller->add_edit_event(
            null,
            $this->eventRepository,
            $request,
            $this->entityManager,
            $this->fileUploadManager
        );

        // Assertions sur la réponse
        $this->assertInstanceOf(Response::class, $response);

        // Assertions sur les paramètres transmis au template
        $this->assertEquals('Création', $capturedParams['texts']['titre']);
        $this->assertEquals('Créer', $capturedParams['texts']['verb']);
        $this->assertArrayHasKey('form', $capturedParams);
    }

    public function testEditEventRendersTemplate(): void
    {
        $user = new User();
        $this->controller->method('getUser')->willReturn($user);

        $event = new Event();
        $this->eventRepository->method('find')->willReturn($event);

        $this->controller->method('createForm')->willReturn($this->form);
        $this->form->method('isSubmitted')->willReturn(false);

        $capturedParams = null;
        $this->controller->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($template, $params) use (&$capturedParams) {
                $capturedParams = $params;
                return new Response();
            });

        $request = new Request();
        $response = $this->controller->add_edit_event(
            1,
            $this->eventRepository,
            $request,
            $this->entityManager,
            $this->fileUploadManager
        );

        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals('Modification', $capturedParams['texts']['titre']);
        $this->assertEquals('Modifier', $capturedParams['texts']['verb']);
        $this->assertArrayHasKey('form', $capturedParams);
    }

    public function testAddEventFormSubmittedAndValidCallsPersistAndFlush(): void
    {
        $user = new User();
        $this->controller->method('getUser')->willReturn($user);
        $this->controller->method('createForm')->willReturn($this->form);

        $this->form->method('isSubmitted')->willReturn(true);
        $this->form->method('isValid')->willReturn(true);

        $event = new Event();
        $this->form->method('getData')->willReturn($event);

        // Vérifie que persist et flush sont appelés (verify)
        $this->entityManager->expects($this->once())->method('persist')->with($event);
        $this->entityManager->expects($this->once())->method('flush');

        // Capture de la redirection
        $capturedRoute = null;
        $capturedParams = null;
        $this->controller->expects($this->once())
            ->method('redirectToRoute')
            ->willReturnCallback(function ($route, $params) use (&$capturedRoute, &$capturedParams) {
                $capturedRoute = $route;
                $capturedParams = $params;
                return new Response();
            });

        $request = new Request();
        $response = $this->controller->add_edit_event(
            null,
            $this->eventRepository,
            $request,
            $this->entityManager,
            $this->fileUploadManager
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('events_detail_id', $capturedRoute);
        $this->assertEquals($event->getId(), $capturedParams['id']);
    }

    public function testDetailId(): void
    {
        // --- Préparation (Arrange) des objets  ---
        // Création d'un utilisateur avec le rôle USER
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        // Création d'un seul événement simulé
        $event = $this->createMock(Event::class);

        // Préparation des vérifications des appels de méthodes et de leurs comportements
        $this->controller->expects($this->once())->method('getUser')->willReturn($user);
        $event->expects($this->once()) ->method('getRegistrations')->willReturn([]);
        $event->method('getId')->willReturn(123);
        $this->eventRepository->expects($this->once())->method('findUpcomingEvents')->willReturn([$event]);
        $capturedTemplate = null; $capturedParams = null;
        $this->controller->expects($this->once())->method('render')
            ->willReturnCallback(function ($template, $params) use (&$capturedTemplate, &$capturedParams) {
                $capturedTemplate = $template; $capturedParams = $params; return new Response();
            });

        // --- Exécution (Act) de la méthode testée---
        $response = $this->controller->detail_id(123, $this->eventRepository);

        // --- Vérifications (Assert) par les assertions sur les résultats et les paramètres capturés ---
        $this->assertInstanceOf(Response::class, $response, 'La méthode doit renvoyer une Response.');
        $this->assertEquals('events/detail_event.html.twig', $capturedTemplate, 'Template attendu.');
        $this->assertEquals(0, $capturedParams['eventIndex'], 'Avec un seul événement, index attendu = 0.');
        $this->assertEquals(123, $capturedParams['id'], 'L\'id transmis doit être celui demandé.');
        $this->assertCount(1, $capturedParams['events'], 'Une seule event doit être transmise.');
        $this->assertSame($event, $capturedParams['events'][0], 'L\'événement transmis doit être l\'objet retourné par le repo.');
        $this->assertArrayHasKey(123, $capturedParams['registrations'], 'Les inscriptions doivent être indexées par l\'id de l\'événement.');
    }

}
