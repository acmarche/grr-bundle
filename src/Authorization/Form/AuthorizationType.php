<?php

namespace Grr\GrrBundle\Authorization\Form;

use Doctrine\ORM\QueryBuilder;
use Grr\Core\Model\AuthorizationModel;
use Grr\GrrBundle\Area\Form\Type\AreaSelectType;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Room\Repository\RoomRepository;
use Grr\GrrBundle\User\Form\Type\RoleSelectType;
use Grr\GrrBundle\User\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorizationType extends AbstractType
{
    /**
     * @var UserRepository
     */
    public $userRepository;
    /**
     * @var RoomRepository
     */
    private $roomRepository;

    public function __construct(
        UserRepository $userRepository,
        RoomRepository $roomRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roomRepository = $roomRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role', RoleSelectType::class, )
            ->add(
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
                'attr' => ['class' => 'custom-select my-1 mr-sm-2 room-select'],
                'multiple' => true,
            ];

            if (null !== $area) {
                $options['query_builder'] = function (RoomRepository $roomRepository) use ($area): QueryBuilder {
                    return $roomRepository->getRoomsByAreaQueryBuilder($area);
                };
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
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getArea());
            }
        );

        $builder->get('area')->addEventListener(
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

    public function onPresetData(FormEvent $event): void
    {
    }

    public function onPostSubmit(FormEvent $event): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => AuthorizationModel::class,
            ]
        );
    }
}
