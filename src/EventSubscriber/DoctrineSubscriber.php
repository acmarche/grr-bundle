<?php

namespace Grr\GrrBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Grr\GrrBundle\Entity\Entry;
use Symfony\Component\Security\Core\Security;

class DoctrineSubscriber implements EventSubscriber
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents()
    {
        return [
            //  Events::postPersist,
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof Entry) {
            return;
        }
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());
        $username = $this->getUsername();
        $entity->setCreatedBy($username);
        $entity->setBeneficiaire($username);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof Entry) {
            return;
        }
        $entity->setUpdatedAt(new \DateTime());
    }

    protected function getUsername(): string
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new \Exception('You must login');
        }

        return $user->getUsername();
    }
}
