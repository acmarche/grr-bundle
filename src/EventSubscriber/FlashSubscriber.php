<?php

namespace Grr\GrrBundle\EventSubscriber;

use Grr\Core\Area\Events\AreaEventAssociatedTypeEntry;
use Grr\Core\Area\Events\AreaEventCreated;
use Grr\Core\Area\Events\AreaEventDeleted;
use Grr\Core\Area\Events\AreaEventUpdated;
use Grr\Core\Authorization\Events\AuthorizationEventCreated;
use Grr\Core\Authorization\Events\AuthorizationEventDeleted;
use Grr\Core\Authorization\Events\AuthorizationEventUpdated;
use Grr\Core\Entry\Events\EntryEventCreated;
use Grr\Core\Entry\Events\EntryEventDeleted;
use Grr\Core\Entry\Events\EntryEventUpdated;
use Grr\Core\Password\Events\PasswordEventUpdated;
use Grr\Core\Room\Events\RoomEventCreated;
use Grr\Core\Room\Events\RoomEventDeleted;
use Grr\Core\Room\Events\RoomEventUpdated;
use Grr\Core\Setting\Events\SettingEventUpdated;
use Grr\Core\TypeEntry\Events\TypeEntryEventCreated;
use Grr\Core\TypeEntry\Events\TypeEntryEventDeleted;
use Grr\Core\TypeEntry\Events\TypeEntryEventUpdated;
use Grr\Core\User\Events\UserEventCreated;
use Grr\Core\User\Events\UserEventDeleted;
use Grr\Core\User\Events\UserEventUpdated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FlashSubscriber implements EventSubscriberInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function onTypeEntryDeleted(): void
    {
        $this->flashBag->add('success', 'flash.typeEntry.deleted');
    }

    public function onTypeEntryUpdated(): void
    {
        $this->flashBag->add('success', 'flash.typeEntry.updated');
    }

    public function onTypeEntryCreated(): void
    {
        $this->flashBag->add('success', 'flash.typeEntry.created');
    }

    public function onRoomDeleted(): void
    {
        $this->flashBag->add('success', 'flash.room.deleted');
    }

    public function onRoomUpdated(): void
    {
        $this->flashBag->add('success', 'flash.room.updated');
    }

    public function onRoomCreated(): void
    {
        $this->flashBag->add('success', 'flash.room.created');
    }

    public function onSettingUpdated(): void
    {
        $this->flashBag->add('success', 'flash.setting.updated');
    }

    public function onUserDeleted(): void
    {
        $this->flashBag->add('success', 'flash.user.deleted');
    }

    public function onUserUpdated(): void
    {
        $this->flashBag->add('success', 'flash.user.updated');
    }

    public function onUserCreated(): void
    {
        $this->flashBag->add('success', 'flash.user.created');
    }

    public function onEntryCreated(): void
    {
        $this->flashBag->add('success', 'flash.entry.created');
    }

    public function onEntryUpdated(): void
    {
        $this->flashBag->add('success', 'flash.entry.updated');
    }

    public function onEntryDeleted(): void
    {
        $this->flashBag->add('success', 'flash.entry.deleted');
    }

    public function onAreaDeleted(): void
    {
        $this->flashBag->add('success', 'flash.area.deleted');
    }

    public function onAreaUpdated(): void
    {
        $this->flashBag->add('success', 'flash.area.updated');
    }

    public function onAreaCreated(): void
    {
        $this->flashBag->add('success', 'flash.area.created');
    }

    public function onAuthorizationDeleted(): void
    {
        $this->flashBag->add('success', 'flash.authorization.deleted');
    }

    public function onAuthorizationCreated(): void
    {
        $this->flashBag->add('success', 'flash.authorization.created');
    }

    public function onAreaAssociatedTypeEntry(): void
    {
        $this->flashBag->add('success', 'flash.area.setTypeEntry');
    }

    public function onPasswordUpdated(): void
    {
        $this->flashBag->add('success', 'flash.password.updated');
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EntryEventCreated::class => 'onEntryCreated',
            EntryEventUpdated::class => 'onEntryUpdated',
            EntryEventDeleted::class => 'onEntryDeleted',

            AreaEventCreated::class => 'onAreaCreated',
            AreaEventUpdated::class => 'onAreaUpdated',
            AreaEventDeleted::class => 'onAreaDeleted',
            AreaEventAssociatedTypeEntry::class => 'onAreaAssociatedTypeEntry',

            RoomEventCreated::class => 'onRoomCreated',
            RoomEventUpdated::class => 'onRoomUpdated',
            RoomEventDeleted::class => 'onRoomDeleted',

            TypeEntryEventCreated::class => 'onTypeEntryCreated',
            TypeEntryEventUpdated::class => 'onTypeEntryUpdated',
            TypeEntryEventDeleted::class => 'onTypeEntryDeleted',

            UserEventCreated::class => 'onUserCreated',
            UserEventUpdated::class => 'onUserUpdated',
            UserEventDeleted::class => 'onUserDeleted',
            PasswordEventUpdated::class => 'onPasswordUpdated',

            AuthorizationEventCreated::class => 'onAuthorizationCreated',
            AuthorizationEventDeleted::class => 'onAuthorizationDeleted',

            SettingEventUpdated::class => 'onSettingUpdated',
        ];
    }
}
