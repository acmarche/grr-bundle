<?php

namespace Grr\GrrBundle\Security\Voter;

use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entity\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RoomVoter extends Voter
{
    public const INDEX = 'grr.room.index';

    public const ADD_ENTRY = 'grr.addEntry';

    public const SHOW = 'grr.room.show';

    public const EDIT = 'grr.room.edit';

    public const DELETE = 'grr.room.delete';

    private ?User $user = null;

    private Room $room;

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
        private readonly AuthorizationHelper $authorizationHelper
    ) {
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        if ($subject && ! $subject instanceof Room) {
            return false;
        }

        return \in_array(
            $attribute,
            [self::INDEX, self::ADD_ENTRY, self::SHOW, self::EDIT, self::DELETE],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $room, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (! $user instanceof User) {
            return false;
        }

        $this->user = $user;
        $this->room = $room;

        if ($user->hasRole(SecurityRole::ROLE_GRR_ADMINISTRATOR)) {
            return true;
        }

        /*
         * not work with test
         */
        if ($this->accessDecisionManager->decide($token, [SecurityRole::ROLE_GRR_ADMINISTRATOR])) {
            //    return true;
        }

        return match ($attribute) {
            self::INDEX => $this->canIndex(),
            self::ADD_ENTRY => $this->canAddEntry(),
            self::SHOW => $this->canView(),
            self::EDIT => $this->canEdit(),
            self::DELETE => $this->canDelete(),
            default => false,
        };
    }

    /**
     * No rule.
     */
    private function canIndex(): bool
    {
        return true;
    }

    private function canAddEntry(): bool
    {
        return $this->authorizationHelper->canAddEntry($this->room, $this->user);
    }

    /**
     * See in admin.
     */
    private function canView(): bool
    {
        if ($this->canEdit()) {
            return true;
        }

        return $this->authorizationHelper->isRoomManager($this->user, $this->room);
    }

    private function canEdit(): bool
    {
        return $this->authorizationHelper->isRoomAdministrator($this->user, $this->room);
    }

    private function canDelete(): bool
    {
        return $this->canEdit();
    }
}
