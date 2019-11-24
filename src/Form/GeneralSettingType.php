<?php

namespace Grr\GrrBundle\Form;

use Grr\Core\Setting\SettingConstants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneralSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                SettingConstants::TITLE_HOME_PAGE,
                TextType::class,
                [
                    'required' => false,
                    'label' => 'setting.titlehomepage.label',
                    'help' => 'setting.titlehomepage.help',
                ]
            )
            ->add(
                SettingConstants::MESSAGE_HOME_PAGE,
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'setting.messagehomepage.label',
                    'help' => 'setting.messagehomepage.help',
                ]
            )
            ->add(
                SettingConstants::COMPANY,
                TextType::class,
                [
                    'required' => true,
                    'label' => 'setting.company.label',
                    'help' => 'setting.compagny.help',
                ]
            )
            ->add(
                SettingConstants::WEBMASTER_NAME,
                TextType::class,
                [
                    'required' => false,
                    'label' => 'setting.webmastername.label',
                    'help' => 'setting.webmastername.help',
                ]
            )
            ->add(
                SettingConstants::WEBMASTER_EMAIL,
                CollectionType::class,
                [
                    'required' => false,
                    'entry_type' => EmailType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'label' => 'setting.webmasteremail.label',
                    'help' => 'setting.webmasteremail.help',
                ]
            )
            ->add(
                SettingConstants::TECHNICAL_SUPPORT_EMAIL,
                CollectionType::class,
                [
                    'required' => true,
                    'entry_type' => EmailType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'label' => 'setting.technicalsupportemail.label',
                    'help' => 'setting.technicalsupportemail.help',
                ]
            )
            ->add(
                SettingConstants::NB_CALENDAR,
                IntegerType::class,
                [
                    'required' => true,
                    'label' => 'setting.nbcalendar.label',
                    'help' => 'setting.nbcalendar.help',
                ]
            )
            ->add(
                SettingConstants::MESSAGE_ACCUEIL,
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'setting.messageaccueil.label',
                    'help' => 'setting.messageaccueil.help',
                ]
            )
            ->add(
                SettingConstants::BEGIN_BOOKINGS,
                DateType::class,
                [
                    'required' => false,
                    'label' => 'setting.beginbooking.label',
                    'help' => 'setting.beginbooking.help',
                ]
            )
            ->add(
                SettingConstants::END_BOOKINGS,
                DateType::class,
                [
                    'required' => false,
                    'label' => 'setting.endbooking.label',
                    'help' => 'setting.endbooking.help',
                ]
            )
            ->add(
                SettingConstants::DEFAULT_LANGUAGE,
                ChoiceType::class,
                [
                    'required' => true,
                    'choices' => ['fr' => 'fr'],
                    'label' => 'setting.defaultlanguage.label',
                    'help' => 'setting.defaultlanguage.help',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            ]
        );
    }
}
