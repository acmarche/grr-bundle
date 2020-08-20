<?php

namespace Grr\GrrBundle\Security\Voter;

use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See http://symfony.com/doc/current/security/voters.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class AreaVoter extends Voter
{
    // Defining these constants is overkill for this simple application, but for real
    // applications, it's a recommended practice to avoid relying on "magic strings"
    const INDEX = 'grr.area.index';
    const NEW = 'grr.area.new';
    const NEW_ROOM = 'grr.area.new.room';
    const SHOW = 'grr.area.show';
    const EDIT = 'grr.area.edit';
    const DELETE = 'grr.area.delete';
    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    /**
     * @var User
     */
    private $user;
    /**
     * @var AuthorizationHelper
     */
    private $authorizationHelper;
    /**
     * @var Area
     */
    private $area;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager, AuthorizationHelper $authorizationHelper)
    {
        $this->accessDecisionManager = $accessDecisionManager;
        $this->authorizationHelper = $authorizationHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        if ($subject && !$subject instanceof Area) {
            return false;
        }

        return in_array(
            $attribute,
            [self::INDEX, self::NEW, self::NEW_ROOM, self::SHOW, self::EDIT, self::DELETE],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $area, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $this->user = $user;
        $this->area = $area;

        if ($user->hasRole(SecurityRole::ROLE_GRR_ADMINISTRATOR)) {
            return true;
        }

        /*
         * not work with test
         */
        if ($this->accessDecisionManager->decide($token, [SecurityRole::ROLE_GRR_ADMINISTRATOR])) {
            //   return true;
        }

        switch ($attribute) {
            case self::INDEX:
                return $this->canIndex();
            case self::NEW:
                return $this->canNew();
            case self::NEW_ROOM:
                return $this->canNewRoom();
            case self::SHOW:
                return $this->canView();
            case self::EDIT:
                return $this->canEdit();
            case self::DELETE:
                return $this->canDelete();
        }

        return false;
    }

    private function canIndex(): bool
    {
        return true;
    }

    private function canNew(): bool
    {
        return false;
    }

    private function canNewRoom(): bool
    {
        return $this->authorizationHelper->isAreaAdministrator($this->user, $this->area);
    }

    /**
     * See in admin.
     */
    private function canView(): bool
    {
        if ($this->canEdit()) {
            return true;
        }

        return $this->authorizationHelper->isAreaManager($this->user, $this->area);
    }

    private function canEdit(): bool
    {
        return $this->authorizationHelper->isAreaAdministrator($this->user, $this->area);
    }

    private function canDelete(): bool
    {
        return $this->canEdit();
    }
}
