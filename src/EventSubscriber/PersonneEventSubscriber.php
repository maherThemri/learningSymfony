<?php

namespace App\EventSubscriber;

use App\Event\AddPersonneEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonneEventSubscribe implements EventSubscriberInterface
{
    public function __construct(
        private MailerService $mailer
    ) {
    }
    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            AddPersonneEvent::ADD_PERSONNE_EVENT => ['onAddPersonneEvent', 3000]
        ];
    }
    public function onAddPersonneEvent(AddPersonneEvent $event)
    {
        $personne = $event->getPersonne();
        $mailMessage = $personne->getName() . ' ' . $personne->getFirstName() . ' ' . $message;

        // envoyer email
        $this->mailer->sendEmail(content: $mailMessage, subject: 'Mail sent from eventSubscriber');
    }
}
