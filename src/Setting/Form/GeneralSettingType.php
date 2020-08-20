<?php

namespace Grr\GrrBundle\Setting\Form;

use Grr\Core\Setting\SettingConstants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                SettingConstants::TITLE_HOME_PAGE,
                TextType::class,
                [
                    'required' => false,
                    'label' => 'label.setting.title_homepage',
                    'help' => 'help.setting.title_homepage',
                ]
            )
            ->add(
                SettingConstants::MESSAGE_HOME_PAGE,
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'label.setting.message_homepage',
                    'help' => 'help.setting.message_homepage',
                ]
            )
            ->add(
                SettingConstants::COMPANY,
                TextType::class,
                [
                    'required' => true,
                    'label' => 'label.setting.company',
                    'help' => 'help.setting.compagny',
                ]
            )
            ->add(
                SettingConstants::WEBMASTER_NAME,
                TextType::class,
                [
                    'required' => false,
                    'label' => 'label.setting.webmaster_name',
                    'help' => 'help.setting.webmaster_name',
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
                    'label' => 'label.setting.webmaster_email',
                    'help' => 'help.setting.webmaster_email',
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
                    'label' => 'label.setting.technical_support_email',
                    'help' => 'help.setting.technical_support_email',
                ]
            )
            ->add(
                SettingConstants::NB_CALENDAR,
                IntegerType::class,
                [
                    'required' => true,
                    'label' => 'label.setting.nbcalendar',
                    'help' => 'help.setting.nbcalendar',
                ]
            )
            ->add(
                SettingConstants::MESSAGE_ACCUEIL,
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'label.setting.message_accueil',
                    'help' => 'help.setting.message_accueil',
                ]
            )
            ->add(
                SettingConstants::BEGIN_BOOKINGS,
                DateType::class,
                [
                    'required' => false,
                    'label' => 'label.setting.begin_booking',
                    'help' => 'help.setting.begin_booking',
                ]
            )
            ->add(
                SettingConstants::END_BOOKINGS,
                DateType::class,
                [
                    'required' => false,
                    'label' => 'label.setting.end_booking',
                    'help' => 'help.setting.end_booking',
                ]
            )
            ->add(
                SettingConstants::DEFAULT_LANGUAGE,
                ChoiceType::class,
                [
                    'required' => true,
                    'choices' => ['fr' => 'fr'],
                    'label' => 'label.setting.default_language',
                    'help' => 'label.setting.default_language',
                ]
            )
            ->add(
                SettingConstants::SEND_ALWAYS_MAIL_TO_CREATOR,
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'label.setting.send_always_mail_to_creator',
                    'help' => 'help.setting.send_always_mail_to_creator',
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
            ]
        );
    }
}
