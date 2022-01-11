<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 5/09/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Authorization\Form;

use Doctrine\ORM\QueryBuilder;
use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\GrrBundle\Area\Form\Type\AreaSelectType;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Room\Repository\RoomRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class AuthorizationUserType extends AbstractType
{
    public function __construct(
        public UserRepositoryInterface $userRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add(
            'area',
            AreaSelectType::class,
            [
                'placeholder' => 'placeholder.area.select',
            ]
        );

        $formModifier = function (FormInterface $form, Area $area = null) {
            $options = [
                'class' => Room::class,
                'label' => 'label.room.multiple_select',
                'placeholder' => '',
                'attr' => [
                    'class' => 'custom-select my-1 mr-sm-2 room-select',
                ],
                'multiple' => true,
            ];

            if (null !== $area) {
                $options['query_builder'] = fn (RoomRepository $roomRepository): QueryBuilder => $roomRepository->getRoomsByAreaQueryBuilder($area);
            } else {
                $options['choices'] = [];
            }

            $form->add(
                'rooms',
                EntityType::class,
                $options
            );
        };

        /*
         * Sert à valider les ressources sélectionnées lors de l'envoie du form
         * Nécessaire car à l'init du form, la liste est vide.
         */
        $formBuilder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getArea());
            }
        );

        $formBuilder->get('area')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $area = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $area);
            }
        );
    }

    public function getParent(): ?string
    {
        return AuthorizationType::class;
    }
}
