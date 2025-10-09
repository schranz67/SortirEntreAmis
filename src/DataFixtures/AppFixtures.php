<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}
    public function load(ObjectManager $manager): void
    {
        # Création des catégories
        $category1 = new Category();
        $category1->setName('Culture');
        $manager->persist($category1);
        $category2 = new Category();
        $category2->setName('Sport');
        $manager->persist($category2);
        $category3 = new Category();
        $category3->setName('Gastronomie');
        $manager->persist($category3);

        # Création des places
        $place1 = new Place();
        $place1->setName('Médiathèque Haguenau');
        $manager->persist($place1);
        $place2 = new Place();
        $place2->setName('Médiathèque Bischwiller');
        $manager->persist($place2);
        $place3 = new Place();
        $place3->setName('Maison des sports');
        $manager->persist($place3);

        # Création d'un administrateur et d'un utilisateur
        $admin = new User();
        $admin->setEmail('admin@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $password = $this->hasher->hashPassword($admin, 'admin1234');
        $admin->setPassword($password);
        $admin->setName('admin');
        $admin->setFirstname('admin');
        $manager->persist($admin);
        $user = new User();
        $user->setEmail('user@gmail.com');
        $user->setRoles(['ROLE_USER']);
        $password = $this->hasher->hashPassword($user, 'user1234');
        $user->setPassword($password);
        $user->setName('user');
        $user->setFirstname('user');
        $manager->persist($user);

        # Création de 5 évènements
        $events = [];

        $event1 = new Event();
        $event1->setTitle('Piscine');
        $event1->setCategory($category2);
        $event1->setPlace($place3);
        $event1->setStart(new \DateTime('2025-10-08 10:30:00'));
        $events[] = $event1;

        $event2 = new Event();
        $event2->setTitle('Lecture de livres');
        $event2->setCategory($category1);
        $event2->setPlace($place1);
        $event2->setStart(new \DateTime('2025-10-08 15:00:00'));
        $events[] = $event2;

        $event3 = new Event();
        $event3->setTitle('Conte pour enfant');
        $event3->setCategory($category1);
        $event3->setPlace($place2);
        $event3->setStart(new \DateTime('2025-10-09 15:00:00'));
        $events[] = $event3;

        $event4 = new Event();
        $event4->setTitle('Football');
        $event4->setCategory($category2);
        $event4->setPlace($place3);
        $event4->setStart(new \DateTime('2025-10-09 16:30:00'));
        $events[] = $event4;

        $event5 = new Event();
        $event5->setTitle('Repas sportif');
        $event5->setCategory($category2);
        $event5->setPlace($place3);
        $event5->setStart(new \DateTime('2025-10-10 14:00:00'));
        $events[] = $event5;

        foreach ($events as $event) {
            $event->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.');
            $event->setOrganizer($admin);
            $manager->persist($event);
        }

        # Sauvegarder les changements dans la base de données
        $manager->flush();
    }
}
