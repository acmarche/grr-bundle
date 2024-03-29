<?php

namespace Grr\GrrBundle\Authorization\Form;

use Doctrine\ORM\QueryBuilder;
use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\Core\Model\AuthorizationModel;
use Grr\GrrBundle\Area\Form\Type\AreaSelectType;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Room\Repository\RoomRepository;
use Grr\GrrBundle\User\Form\Type\RoleSelectType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorizationType extends AbstractType
{
    public function __construct(
        public UserRepositoryInterface $userRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('role', RoleSelectType::class, )
            ->add(
                'area',
                AreaSelectType::class,
                [
                    'placeholder' => 'placeholder.area.select',
                ]
            );

        $formModifier = static function (FormInterface $form, Area $area = null) : void {
            $options = [
                'class' => Room::class,
                'label' => 'label.room.multiple_select',
                'placeholder' => '',
                'attr' => [
                    'class' => 'my-1 mr-sm-2',
                ],
                'multiple' => true,
            ];
            if ($area instanceof Area) {
                $options['query_builder'] = static fn(RoomRepository $roomRepository): QueryBuilder => $roomRepository->getRoomsByAreaQueryBuilder($area);
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
            static function (FormEvent $event) use ($formModifier) : void {
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getArea());
            }
        );

        $formBuilder->get('area')->addEventListener(
            FormEvents::POST_SUBMIT,
            static function (FormEvent $event) use ($formModifier) : void {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $area = $event->getForm()->getData();
                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $area);
            }
        );
    }

    public function onPresetData(): void
    {
    }

    public function onPostSubmit(): void
    {
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => AuthorizationModel::class,
            ]
        );
    }
}
