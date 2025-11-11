<?php

namespace App\Tests\Controller;

use App\Controller\EventController;
use App\Entity\Event;
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

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->fileUploadManager = $this->createMock(FileUploadManager::class);
        $this->form = $this->createMock(FormInterface::class);

        $this->controller = $this->getMockBuilder(EventController::class)
            ->onlyMethods(['getUser', 'createForm', 'render', 'redirectToRoute', 'getParameter'])
            ->getMock();
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
}
