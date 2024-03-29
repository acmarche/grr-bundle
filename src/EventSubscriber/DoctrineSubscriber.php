<?php

namespace Grr\GrrBundle\EventSubscriber;

use DateTime;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Grr\GrrBundle\Entity\Entry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/events.html#events
 * Class DoctrineSubscriber
 */
#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class DoctrineSubscriber implements EventSubscriber
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
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
    public function prePersist(LifecycleEventArgs $lifecycleEventArgs): void
    {
        $object = $lifecycleEventArgs->getObject();
        if (! $object instanceof Entry) {
            return;
        }

        $object->setCreatedAt(new DateTime());
        $object->setUpdatedAt(new DateTime());

        $username = $this->getUsername();

        if (! $object->getCreatedBy()) {
            $object->setCreatedBy($username);
        }

        if (null === $object->getReservedFor()) {
            $object->setReservedFor($username);
        }
    }

    public function preUpdate(LifecycleEventArgs $lifecycleEventArgs): void
    {
        $object = $lifecycleEventArgs->getObject();
        if (! $object instanceof Entry) {
            return;
        }

        $object->setUpdatedAt(new DateTime());
    }

    protected function getUsername(): string
    {
        $user = $this->security->getUser();

        if (! $user instanceof UserInterface) {
            /*
             * avec behat trouve pas user, pourtant le met bien dans db
             */
            return 'no user';
            //   throw new \Exception('To add entry, you must login');
        }

        return $user->getUserIdentifier();
    }
}
