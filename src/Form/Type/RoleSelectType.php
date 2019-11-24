<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 9/09/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Form\Type;

use Grr\GrrBundle\Form\DataTransformer\StdClassToNumberTransformer;
use Grr\Core\Security\SecurityRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleSelectType extends AbstractType
{
    /**
     * @var StdClassToNumberTransformer
     */
    private $transformer;

    public function __construct(StdClassToNumberTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $roles = SecurityRole::getRolesForAuthorization();

        $resolver->setDefaults(
            [
                'choices' => $roles,
                'label' => 'authorization.area.role.label',
                'placeholder' => 'none.male',
                'choice_label' => function ($role) {
                    return $role->name;
                },
                'choice_value' => function ($role) {
                    if (null == $role) {
                        return null;
                    }

                    return $role->value;
                },
                'description' => function ($role) {
                    return $role->description;
                },
                'required' => false,
                'multiple' => false,
                'expanded' => true,
                'attr' => ['class' => 'authorization_role'], //for js
            ]
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        //j'essaie d'afficher la description
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
