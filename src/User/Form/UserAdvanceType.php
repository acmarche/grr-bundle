<?php

namespace Grr\GrrBundle\User\Form;

use Grr\Core\I18n\LocalHelper;
use Grr\GrrBundle\Area\Form\Type\AreaSelectType;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\EventSubscriber\Form\AddRoomFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAdvanceType extends AbstractType
{
    /**
     * @var LocalHelper
     */
    private $localHelper;

    public function __construct(LocalHelper $localHelper)
    {
        $this->localHelper = $localHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'isEnabled',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'label.user.is_enabled',
                    'help' => 'help.user.is_enabled',
                ]
            )
            ->add(
                'languageDefault',
                ChoiceType::class,
                [
                    'required' => false,
                    'label' => 'label.user.languageDefault',
                    'choices' => $this->localHelper->getSupportedLocales(),
                ]
            )
            ->add(
                'area',
                AreaSelectType::class,
                [
                    'label' => 'label.user.area_select',
                    'required' => false,
                    'placeholder' => 'placeholder.area.select',
                ]
            )
            ->addEventSubscriber(
                new AddRoomFieldSubscriber(
                    false,
                    'label.user.room_select',
                    'placeholder.room.select_empty'
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }

    public function getParent(): string
    {
        return UserType::class;
    }
}
