<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 15/03/19
 * Time: 22:00.
 */

namespace Grr\GrrBundle\EventSubscriber\Form;

use Grr\GrrBundle\Area\Form\Type\AreaSelectType;
use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Grr\GrrBundle\Entity\Security\User;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;

class AddAreaFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var AuthorizationHelper
     */
    private $authorizationHelper;

    public function __construct(Security $security, AuthorizationHelper $authorizationHelper)
    {
        $this->security = $security;
        $this->authorizationHelper = $authorizationHelper;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }

    public function onPreSetData(FormEvent $formEvent): void
    {
        /**
         * @var User
         */
        $user = $this->security->getUser();
        if (!$user) {
            throw new LogicException('The TypeEntryForm cannot be used without an authenticated user!');
        }

        $options = ['required' => true];

        $areas = $this->authorizationHelper->getAreasUserCanAdd($user);
        $options['choices'] = $areas;

        /**
         * @var FormInterface
         */
        $form = $formEvent->getForm();

        $form->add(
            'area',
            AreaSelectType::class,
            $options
        );
    }
}
