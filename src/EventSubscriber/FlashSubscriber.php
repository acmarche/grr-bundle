<?php

namespace Grr\GrrBundle\EventSubscriber;

use Grr\Core\Area\Events\AreaEventAssociatedEntryType;
use Grr\Core\Area\Events\AreaEventCreated;
use Grr\Core\Area\Events\AreaEventDeleted;
use Grr\Core\Area\Events\AreaEventUpdated;
use Grr\Core\Authorization\Events\AuthorizationEventCreated;
use Grr\Core\Authorization\Events\AuthorizationEventDeleted;
use Grr\Core\Authorization\Events\AuthorizationEventUpdated;
use Grr\Core\Entry\Events\EntryEventCreated;
use Grr\Core\Entry\Events\EntryEventDeleted;
use Grr\Core\Entry\Events\EntryEventUpdated;
use Grr\Core\EntryType\Events\EntryTypeEventCreated;
use Grr\Core\EntryType\Events\EntryTypeEventDeleted;
use Grr\Core\EntryType\Events\EntryTypeEventUpdated;
use Grr\Core\Password\Events\PasswordEventUpdated;
use Grr\Core\Room\Events\RoomEventCreated;
use Grr\Core\Room\Events\RoomEventDeleted;
use Grr\Core\Room\Events\RoomEventUpdated;
use Grr\Core\Setting\Events\SettingEventUpdated;
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

    public function onEntryTypeDeleted(EntryTypeEventDeleted $entryTypeEvent): void
    {
        $this->flashBag->add('success', 'typeEntry.flash.delete');
    }

    public function onEntryTypeUpdated(EntryTypeEventUpdated $entryTypeEvent): void
    {
        $this->flashBag->add('success', 'typeEntry.flash.edit');
    }

    public function onEntryTypeCreated(EntryTypeEventCreated $entryTypeEvent): void
    {
        $this->flashBag->add('success', 'typeEntry.flash.new');
    }

    public function onRoomDeleted(RoomEventDeleted $roomEvent): void
    {
        $this->flashBag->add('success', 'room.flash.delete');
    }

    public function onRoomUpdated(RoomEventUpdated $roomEvent): void
    {
        $this->flashBag->add('success', 'room.flash.edit');
    }

    public function onRoomCreated(RoomEventCreated $roomEvent): void
    {
        $this->flashBag->add('success', 'room.flash.new');
    }

    public function onSettingUpdated(): void
    {
        $this->flashBag->add('success', 'setting.flash.edit');
    }

    public function onUserDeleted(UserEventDeleted $userEvent): void
    {
        $this->flashBag->add('success', 'user.flash.delete');
    }

    public function onUserUpdated(UserEventUpdated $userEvent): void
    {
        $this->flashBag->add('success', 'user.flash.edit');
    }

    public function onUserCreated(UserEventCreated $userEvent): void
    {
        $this->flashBag->add('success', 'user.flash.new');
    }

    public function onEntryCreated(EntryEventCreated $event): void
    {
        $this->flashBag->add('success', 'entry.flash.new');
    }

    public function onEntryUpdated(EntryEventUpdated $event): void
    {
        $this->flashBag->add('success', 'entry.flash.edit');
    }

    public function onEntryDeleted(EntryTypeEventDeleted $event): void
    {
        $this->flashBag->add('success', 'entry.flash.delete');
    }

    public function onAreaDeleted(AreaEventDeleted $areaEvent): void
    {
        $this->flashBag->add('success', 'area.flash.delete');
    }

    public function onAreaUpdated(AreaEventUpdated $areaEvent): void
    {
        $this->flashBag->add('success', 'area.flash.edit');
    }

    public function onAreaCreated(AreaEventCreated $areaEvent): void
    {
        $this->flashBag->add('success', 'area.flash.new');
    }

    public function onAuthorizationDeleted(AuthorizationEventDeleted $event): void
    {
        $this->flashBag->add('success', 'authorization.flash.delete.success');
    }

    public function onAuthorizationCreated(AuthorizationEventUpdated $event): void
    {
        $this->flashBag->add('success', 'authorization.flash.new');
    }

    public function onAreaAssociatedEntryType(): void
    {
        $this->flashBag->add('success', 'entryType.area.flash');
    }

    public function onPasswordUpdated(PasswordEventUpdated $userEvent): void
    {
        $this->flashBag->add('success', 'user.flash.password');
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
            AreaEventAssociatedEntryType::class => 'onAreaAssociatedEntryType',

            RoomEventCreated::class => 'onRoomCreated',
            RoomEventUpdated::class => 'onRoomUpdated',
            RoomEventDeleted::class => 'onRoomDeleted',

            EntryTypeEventCreated::class => 'onEntryTypeCreated',
            EntryTypeEventUpdated::class => 'onEntryTypeUpdated',
            EntryTypeEventDeleted::class => 'onEntryTypeDeleted',

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
