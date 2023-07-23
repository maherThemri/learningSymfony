<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonneEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class PersonneListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }
    public function onPersonneAdd(AddPersonneEvent $event)
    {
        $this->logger->debug('cc je suis entrain  d ecouter l evenement personne.add et une personne d etre ajoutée  et cest' . $event->getPersonne()->getName());
    }
    public function onListAllPersonnes(ListAllPersonneEvent $event)
    {
        $this->logger->debug("Le nombre de personne dans la base est " . $event->getNbPersonne());
    }
    public function onListAllPersonnes2(ListAllPersonneEvent $event)
    {
        $this->logger->debug("le second listener avec le nbre " . $event->getNbPersonne());
    }
    public function logKernelRequest(KernelEvent $event)
    {
        dd($event->getRequest());
    }
}
