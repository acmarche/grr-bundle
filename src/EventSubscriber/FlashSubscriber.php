<?php

namespace Grr\GrrBundle\EventSubscriber;

use Grr\Core\Events\AreaEvent;
use Grr\Core\Events\AuthorizationEvent;
use Grr\Core\Events\EntryEvent;
use Grr\Core\Events\EntryTypeAreaEvent;
use Grr\Core\Events\EntryTypeEvent;
use Grr\Core\Events\RoomEvent;
use Grr\Core\Events\SettingSuccessEvent;
use Grr\Core\Events\UserEvent;
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

    public function onEntryTypeDelete(EntryTypeEvent $entryTypeEvent): void
    {
        $this->flashBag->add('success', 'typeEntry.flash.delete');
    }

    public function onEntryTypeEdit(EntryTypeEvent $entryTypeEvent): void
    {
        $this->flashBag->add('success', 'typeEntry.flash.edit');
    }

    public function onEntryTypeNew(EntryTypeEvent $entryTypeEvent): void
    {
        $this->flashBag->add('success', 'typeEntry.flash.new');
    }

    public function onRoomDelete(RoomEvent $roomEvent): void
    {
        $this->flashBag->add('success', 'room.flash.delete');
    }

    public function onRoomEdit(RoomEvent $roomEvent): void
    {
        $this->flashBag->add('success', 'room.flash.edit');
    }

    public function onRoomNew(RoomEvent $roomEvent): void
    {
        $this->flashBag->add('success', 'room.flash.new');
    }

    public function onSettingSuccess(): void
    {
        $this->flashBag->add('success', 'setting.flash.edit');
    }

    public function onUserDelete(UserEvent $userEvent): void
    {
        $this->flashBag->add('success', 'user.flash.delete');
    }

    public function onUserEdit(UserEvent $userEvent): void
    {
        $this->flashBag->add('success', 'user.flash.edit');
    }

    public function onUserNew(UserEvent $userEvent): void
    {
        $this->flashBag->add('success', 'user.flash.new');
    }

    public function onEntryNew(EntryEvent $event): void
    {
        $this->flashBag->add('success', 'entry.flash.new');
    }

    public function onEntryEdit(EntryEvent $event): void
    {
        $this->flashBag->add('success', 'entry.flash.edit');
    }

    public function onEntryDelete(EntryEvent $event): void
    {
        $this->flashBag->add('success', 'entry.flash.delete');
    }

    public function onAreaDelete(AreaEvent $areaEvent): void
    {
        $this->flashBag->add('success', 'area.flash.delete');
    }

    public function onAreaEdit(AreaEvent $areaEvent): void
    {
        $this->flashBag->add('success', 'area.flash.edit');
    }

    public function onAreaNew(AreaEvent $areaEvent): void
    {
        $this->flashBag->add('success', 'area.flash.new');
    }

    public function onAuthorizationDelete(AuthorizationEvent $event): void
    {
        $this->flashBag->add('success', 'authorization.flash.delete.success');
    }

    public function onAuthorizationNew(AuthorizationEvent $event): void
    {
        $this->flashBag->add('success', 'authorization.flash.new');
    }

    public function onEditEntryTypeArea(): void
    {
        $this->flashBag->add('success', 'entryType.area.flash');
    }

    public function onUserPassword(UserEvent $userEvent): void
    {
        $this->flashBag->add('success', 'user.flash.password');
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EntryEvent::NEW_SUCCESS => 'onEntryNew',
            EntryEvent::EDIT_SUCCESS => 'onEntryEdit',
            EntryEvent::DELETE_SUCCESS => 'onEntryDelete',

            AreaEvent::NEW_SUCCESS => 'onAreaNew',
            AreaEvent::EDIT_SUCCESS => 'onAreaEdit',
            AreaEvent::DELETE_SUCCESS => 'onAreaDelete',

            RoomEvent::NEW_SUCCESS => 'onRoomNew',
            RoomEvent::EDIT_SUCCESS => 'onRoomEdit',
            RoomEvent::DELETE_SUCCESS => 'onRoomDelete',

            EntryTypeEvent::NEW_SUCCESS => 'onEntryTypeNew',
            EntryTypeEvent::EDIT_SUCCESS => 'onEntryTypeEdit',
            EntryTypeEvent::DELETE_SUCCESS => 'onEntryTypeDelete',

            UserEvent::NEW_SUCCESS => 'onUserNew',
            UserEvent::EDIT_SUCCESS => 'onUserEdit',
            UserEvent::DELETE_SUCCESS => 'onUserDelete',
            UserEvent::CHANGE_PASSWORD_SUCCESS => 'onUserPassword',

            AuthorizationEvent::NEW_SUCCESS => 'onAuthorizationNew',
            AuthorizationEvent::DELETE_SUCCESS => 'onAuthorizationDelete',

            EntryTypeAreaEvent::class => 'onEditEntryTypeArea',
            SettingSuccessEvent::class => 'onSettingSuccess',
        ];
    }
}
