<?php

namespace Grr\GrrBundle\Booking;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BookingForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $horaires = array_flip(BookingCont::horaires);

        $formBuilder
            ->add('room', HiddenType::class)
            ->add(
                'jour',
                DateType::class,
                [
                    'required' => true,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Prénom',
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Téléphone',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'informations',
                TextType::class,
                [
                    'label' => 'Informations complémentaires',
                    'help' => '(installation salle, matériel nécessaire, horaire précis...)',
                    'required' => false,
                ]
            )
            ->add(
                'tva',
                TextType::class,
                [
                    'label' => 'Numéro Tva',
                    'required' => false,
                ]
            )
            ->add('horaire', ChoiceType::class, [
                'choices' => $horaires,
                'expanded' => true,
                'multiple' => false,
            ]);
    }
}
