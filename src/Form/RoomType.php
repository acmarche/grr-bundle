<?php

namespace Grr\GrrBundle\Form;

use Grr\GrrBundle\Entity\Room;
use Grr\Core\Setting\SettingsRoom;
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
                    'label' => 'room.form.nom.label',
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'label' => 'room.form.description.label',
                    'required' => false,
                ]
            )
            ->add(
                'capacity',
                IntegerType::class,
                [
                    'label' => 'room.form.capacity.label',
                    'help' => 'room.form.capacity.help',
                    'required' => false,
                ]
            )
            ->add(
                'maximumBooking',
                IntegerType::class,
                [
                    'label' => 'room.form.maxBooking.label',
                    'help' => 'room.form.maxBooking.help',
                    'required' => false,
                ]
            )
            ->add(
                'statutRoom',
                CheckboxType::class,
                [
                    'label' => 'room.form.statutRoom.label',
                    'help' => 'room.form.statutRoom.help',
                    'required' => false,
                ]
            )
            ->add(
                'showFicRoom',
                CheckboxType::class,
                [
                    'label' => 'room.form.showFicRoom.label',
                    'help' => 'room.form.showFicRoom.help',
                    'required' => false,
                ]
            )
            ->add(
                'pictureRoom',
                FileType::class,
                [
                    'label' => 'room.form.pictureRoom.label',
                    'help' => 'room.form.pictureRoom.help',
                    'required' => false,
                ]
            )
            ->add(
                'commentRoom',
                TextareaType::class,
                [
                    'label' => 'room.form.commentRoom.label',
                    'attr' => ['height' => '80px'],
                    'required' => false,
                ]
            )
            ->add(
                'showComment',
                CheckboxType::class,
                [
                    'label' => 'room.form.showComment.label',
                    'required' => false,
                ]
            )
            ->add(
                'delaisMaxResaRoom',
                IntegerType::class,
                [
                    'label' => 'room.form.delaisMaxResaRoom.label',
                    'help' => 'room.form.delaisMaxResaRoom.help',
                ]
            )
            ->add(
                'delaisMinResaRoom',
                IntegerType::class,
                [
                    'label' => 'room.form.delaisMinResaRoom.label',
                    'help' => 'room.form.delaisMinResaRoom.help',
                ]
            )
            ->add(
                'allowActionInPast',
                CheckboxType::class,
                [
                    'label' => 'room.form.allowActionInPast.label',
                    'help' => 'room.form.allowActionInPast.help',
                    'required' => false,
                ]
            )
            ->add(
                'orderDisplay',
                IntegerType::class,
                [
                    'label' => 'room.form.orderDisplay.label',
                ]
            )
            ->add(
                'delaisOptionReservation',
                IntegerType::class,
                [
                    'label' => 'room.form.delaisOptionReservation.label',
                    'help' => 'room.form.delaisOptionReservation.help',
                ]
            )
            ->add(
                'dontAllowModify',
                CheckboxType::class,
                [
                    'label' => 'room.form.dontAllowModify.label',
                    'help' => 'room.form.dontAllowModify.help',
                    'required' => false,
                ]
            )
            ->add(
                'typeAffichageReser',
                ChoiceType::class,
                [
                    'label' => 'room.form.typeAffichageReser.label',
                    'help' => 'room.form.typeAffichageReser.help',
                    'choices' => array_flip(SettingsRoom::typeAffichageReser()),
                ]
            )
            ->add(
                'moderate',
                CheckboxType::class,
                [
                    'label' => 'room.form.moderate.label',
                    'help' => 'room.form.moderate.help',
                    'required' => false,
                ]
            )
            ->add(
                'quiPeutReserverPour',
                ChoiceType::class,
                [
                    'label' => 'room.form.quiPeutReserverPour.label',
                    'choices' => array_flip(SettingsRoom::whoCanAddFor()),
                ]
            )
            ->add(
                'activeRessourceEmpruntee',
                CheckboxType::class,
                [
                    'label' => 'room.form.activeRessourceEmpruntee.label',
                    'required' => false,
                ]
            )
            ->add(
                'ruleToAdd',
                ChoiceType::class,
                [
                    'label' => 'room.form.authorization.label',
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
