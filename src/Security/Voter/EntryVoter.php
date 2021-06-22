<?php

namespace Grr\GrrBundle\Security\Voter;

use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entity\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class EntryVoter extends Voter
{
    public const INDEX = 'grr.entry.index';
    public const NEW = 'grr.entry.new';
    public const SHOW = 'grr.entry.show';
    public const EDIT = 'grr.entry.edit';
    public const DELETE = 'grr.entry.delete';

    private ?UserInterface $user = null;
    private AuthorizationHelper $authorizationHelper;
    /**
     * @var Entry
     */
    private $entry;
    /**
     * @var Room|null
     */
    private $room;
    /**
     * @var Area
     */
    private $area;
    private Security $security;

    public function __construct(Security $security, AuthorizationHelper $authorizationHelper)
    {
        $this->authorizationHelper = $authorizationHelper;
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        if ($subject && !$subject instanceof Entry) {
            return false;
        }

        return in_array($attribute, [self::INDEX, self::NEW, self::SHOW, self::EDIT, self::DELETE], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $entry, TokenInterface $token): bool
    {
        $user = $this->security->getUser();
        $this->user = $user;
        $this->entry = $entry;
        $this->room = $this->entry->getRoom();
        $this->area = $this->room->getArea();

        if (!$this->isAnonyme() && $this->security->isGranted(SecurityRole::ROLE_GRR_ADMINISTRATOR)) {
            return true;
        }

        switch ($attribute) {
            case self::INDEX:
                return $this->canIndex();
            case self::SHOW:
                return $this->canView();
            case self::EDIT:
                return $this->canEdit();
            case self::DELETE:
                return $this->canDelete();
        }

        return false;
    }

    public function canIndex(): bool
    {
        return true;
    }

    private function canView(): bool
    {
        if ($this->isAnonyme()) {
            if ($this->authorizationHelper->isAreaRestricted($this->area)) {
                return false;
            }

            return $this->authorizationHelper->canSeeRoom();
        }

        if ($this->authorizationHelper->isAreaRestricted($this->area)) {
            return $this->authorizationHelper->canSeeAreaRestricted();
        }

        return $this->authorizationHelper->canSeeRoom();
    }

    private function canEdit(): bool
    {
        if ($this->isAnonyme()) {
            return false;
        }

        return $this->authorizationHelper->canAddEntry($this->room, $this->user);
    }

    private function canDelete(): bool
    {
        if ($this->isAnonyme()) {
            return false;
        }

        return $this->canEdit();
    }

    private function isAnonyme(): bool
    {
        return !($this->user instanceof User);
    }
}
