<?php

namespace Grr\GrrBundle\Form;

use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\EventSubscriber\AddAreaFieldSubscriber;
use Grr\GrrBundle\EventSubscriber\AddDurationFieldSubscriber;
use Grr\GrrBundle\EventSubscriber\AddRoomFieldSubscriber;
use Grr\GrrBundle\EventSubscriber\AddTypeEntryFieldSubscriber;
use Grr\Core\Factory\DurationFactory;
use Grr\GrrBundle\Security\AuthorizationHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class EntryType extends AbstractType
{
    /**
     * @var DurationFactory
     */
    private $durationFactory;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var AuthorizationHelper
     */
    private $authorizationHelper;

    public function __construct(
        DurationFactory $durationFactory,
        Security $security,
        AuthorizationHelper $authorizationHelper
    ) {
        $this->durationFactory = $durationFactory;
        $this->security = $security;
        $this->authorizationHelper = $authorizationHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Entry::class,
            ]
        );
    }
}
