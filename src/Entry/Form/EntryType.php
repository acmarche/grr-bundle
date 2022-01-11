<?php

namespace Grr\GrrBundle\Entry\Form;

use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\Core\Factory\DurationFactory;
use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\EventSubscriber\Form\AddAreaFieldSubscriber;
use Grr\GrrBundle\EventSubscriber\Form\AddDurationFieldSubscriber;
use Grr\GrrBundle\EventSubscriber\Form\AddRoomFieldSubscriber;
use Grr\GrrBundle\EventSubscriber\Form\AddTypeEntryFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class EntryType extends AbstractType
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DurationFactory $durationFactory,
        private Security $security,
        private AuthorizationHelper $authorizationHelper
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'startTime',
                DateTimeType::class,
                [
                    'label' => 'label.entry.startTime',
                    'help' => 'help.entry.startTime',
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'label.entry.name',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'label.entry.description',
                    'required' => false,
                ]
            )
            ->addEventSubscriber(new AddAreaFieldSubscriber($this->security, $this->authorizationHelper))
            ->addEventSubscriber(new AddTypeEntryFieldSubscriber())
            ->addEventSubscriber(new AddDurationFieldSubscriber($this->durationFactory))
            ->addEventSubscriber(new AddRoomFieldSubscriber(true));

        if ($this->security->isGranted(SecurityRole::ROLE_GRR_ADMINISTRATOR)) {
            $formBuilder->add(
                'reservedFor',
                ChoiceType::class,
                [
                    'choices' => $this->userRepository->listReservedFor(),
                    'label' => 'label.entry.reservedFor',
                    'required' => false,
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Entry::class,
            ]
        );
    }
}
