<?php

namespace Grr\GrrBundle\Form\Type;

use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Repository\Security\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSelectType extends AbstractType
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'label' => 'entry.form.user.select.label',
                'class' => User::class,
                'multiple' => true,
                'expanded' => false,
                'query_builder' => $this->userRepository->getQueryBuilder(),
                'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
            ]
        );
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
