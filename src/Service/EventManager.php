<?php

namespace App\Service;

use App\Entity\Event;

class EventManager {

    /**
     * Recherche des inscriptions de plusieurs évènements
     *
     * @param array $events
     *
     * @return array $registrations
     *
     */
    public function getRegistrationEvents(array $events): array
    {
        $registrations=[];
        foreach ($events as $event) {
            $registrations[$event->getId()] = $event->getRegistrations();
        }
        return $registrations;
    }
}
