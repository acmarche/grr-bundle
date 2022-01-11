<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 15/03/19
 * Time: 22:00.
 */

namespace Grr\GrrBundle\EventSubscriber\Form;

use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\GrrBundle\Entity\Security\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddUserFieldSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
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
        $entry = $formEvent->getData();
        $form = $formEvent->getForm();
        $user = $entry->getUsers();

        if ($user) {
            //    $form->add('user', HiddenType::class);
        } else {
            $form->add(
                'users',
                EntityType::class,
                [
                    'label' => 'entry.form.user.select.label',
                    'class' => User::class,
                    'required' => true,
                    'multiple' => true,
                    'expanded' => true,
                    'query_builder' => $this->userRepository->getQueryBuilder(),
                    'attr' => [
                        'class' => 'custom-control custom-checkbox my-1 mr-sm-2',
                    ],
                ]
            );
        }
    }
}
