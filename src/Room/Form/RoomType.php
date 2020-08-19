<?php

namespace Grr\GrrBundle\Room\Form;

use Grr\Core\Setting\SettingsRoom;
use Grr\GrrBundle\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'label.room.name',
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'label' => 'label.room.description',
                    'required' => false,
                ]
            )
            ->add(
                'capacity',
                IntegerType::class,
                [
                    'label' => 'label.room.capacity',
                    'help' => 'help.room.capacity',
                    'required' => false,
                ]
            )
            ->add(
                'maximumBooking',
                IntegerType::class,
                [
                    'label' => 'label.room.maximumBooking',
                    'help' => 'help.room.maximumBooking',
                    'required' => false,
                ]
            )
            ->add(
                'statutRoom',
                CheckboxType::class,
                [
                    'label' => 'label.room.statutRoom',
                    'help' => 'help.room.statutRoom',
                    'required' => false,
                ]
            )
            ->add(
                'showFicRoom',
                CheckboxType::class,
                [
                    'label' => 'label.room.showPicture',
                    'help' => 'help.room.showPicture',
                    'required' => false,
                ]
            )
            ->add(
                'pictureRoom',
                FileType::class,
                [
                    'label' => 'label.room.picture',
                    'help' => 'help.room.picture',
                    'required' => false,
                ]
            )
            ->add(
                'commentRoom',
                TextareaType::class,
                [
                    'label' => 'label.room.comment',
                    'attr' => ['height' => '80px'],
                    'required' => false,
                ]
            )
            ->add(
                'showComment',
                CheckboxType::class,
                [
                    'label' => 'label.room.showComment',
                    'required' => false,
                ]
            )
            ->add(
                'delaisMaxResaRoom',
                IntegerType::class,
                [
                    'label' => 'label.room.delaisMaxResaRoom',
                    'help' => 'help.room.delaisMaxResaRoom',
                ]
            )
            ->add(
                'delaisMinResaRoom',
                IntegerType::class,
                [
                    'label' => 'label.room.delaisMinResaRoom',
                    'help' => 'help.room.delaisMinResaRoom',
                ]
            )
            ->add(
                'allowActionInPast',
                CheckboxType::class,
                [
                    'label' => 'label.room.allow_action_in_past',
                    'help' => 'help.room.allow_action_in_past',
                    'required' => false,
                ]
            )
            ->add(
                'orderDisplay',
                IntegerType::class,
                [
                    'label' => 'label.room.orderDisplay',
                ]
            )
            ->add(
                'delaisOptionReservation',
                IntegerType::class,
                [
                    'label' => 'label.room.delaisOptionReservation',
                    'help' => 'help.room.delaisOptionReservation',
                ]
            )
            ->add(
                'dontAllowModify',
                CheckboxType::class,
                [
                    'label' => 'label.room.dont_allow_modify',
                    'help' => 'help.room.dont_allow_modify',
                    'required' => false,
                ]
            )
            ->add(
                'typeAffichageReser',
                ChoiceType::class,
                [
                    'label' => 'label.room.typeAffichageReser',
                    'help' => 'help.room.typeAffichageReser',
                    'choices' => array_flip(SettingsRoom::typeAffichageReser()),
                ]
            )
            ->add(
                'moderate',
                CheckboxType::class,
                [
                    'label' => 'label.room.moderate',
                    'help' => 'help.room.moderate',
                    'required' => false,
                ]
            )
            ->add(
                'quiPeutReserverPour',
                ChoiceType::class,
                [
                    'label' => 'label.room.quiPeutReserverPour',
                    'choices' => array_flip(SettingsRoom::whoCanAddFor()),
                ]
            )
            ->add(
                'activeRessourceEmpruntee',
                CheckboxType::class,
                [
                    'label' => 'label.room.activeRessourceEmpruntee',
                    'required' => false,
                ]
            )
            ->add(
                'ruleToAdd',
                ChoiceType::class,
                [
                    'label' => 'label.room.rule_to_add',
                    'choices' => array_flip(SettingsRoom::whoCanAdd()),
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Room::class,
            ]
        );
    }
}
