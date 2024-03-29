<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 9/09/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\User\Form\Type;

use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Form\DataTransformer\StdClassToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleSelectType extends AbstractType
{
    public function __construct(
        private readonly StdClassToNumberTransformer $stdClassToNumberTransformer
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->addModelTransformer($this->stdClassToNumberTransformer);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $roles = SecurityRole::getRolesForAuthorization();

        $optionsResolver->setDefaults(
            [
                'choices' => $roles,
                'label' => 'label.role.select',
                'placeholder' => 'none.male',
                'choice_label' => static fn($role) => $role->name,
                'choice_value' => static function ($role) {
                    if (null == $role) {
                        return null;
                    }
                    return $role->value;
                },
                'description' => static fn($role) => $role->description,
                'required' => false,
                'multiple' => false,
                'expanded' => true,
                'attr' => [
                    'data-action' => 'click->authorization#selectRole',
                ],
                //for js
            ]
        );
    }

    public function buildView(FormView $formView, FormInterface $form, array $options): void
    {
        //j'essaie d'afficher la description
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
