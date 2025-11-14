<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerFunctionalTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testListAndDetailFunctionalLogic(): void
    {
        // Création d'un utilisateur
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('password'); // hashé si nécessaire
        $this->entityManager->persist($user);

        // Création d'une place et catégorie
        $place = new Place();
        $place->setName('Salle Test');
        $this->entityManager->persist($place);
        $category = new Category();
        $category->setName('Conférence');
        $this->entityManager->persist($category);

        // Création d'un événement
        $event = new Event();
        $event->setTitle('Événement Test');
        $event->setOrganizer($user);
        $event->setPlace($place);
        $event->setCategory($category);
        $event->setStart(new \DateTime('+1 day'));
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        // Connexion du client
        $this->client->loginUser($user);

        // Test fonctionnel de la liste d'événements
        $this->client->request('GET', '/profile/list_events');
        $responseList = $this->client->getResponse();
        $this->assertTrue($responseList->isSuccessful(), 'La page de liste doit répondre avec un HTTP 200.');
        $listContent = $responseList->getContent();
        $this->assertStringContainsString('Événement Test', $listContent, 'L\'événement créé doit apparaître dans la liste.');

        // Test fonctionnel du détail d'un événement
        $this->client->request('GET', '/profile/detail_event/' . $event->getId());
        $responseDetail = $this->client->getResponse();
        $this->assertTrue($responseDetail->isSuccessful(), 'La page de détail doit répondre avec un HTTP 200.');
        $detailContent = $responseDetail->getContent();
        $this->assertStringContainsString('Événement Test', $detailContent, 'Le détail doit contenir le titre de l\'événement.');
        $this->assertStringContainsString((string)$event->getId(), $detailContent, 'L\'id transmis doit correspondre à l\'événement.');
    }
}
