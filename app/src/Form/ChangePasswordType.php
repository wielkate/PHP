<?php
/**
 * Change password type.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Change password type.
 */
class ChangePasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder builder
     * @param array                $options options
     *
     * @return void return
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('current_password', PasswordType::class, [
                'label' => 'label.current_password',
                'mapped' => false,
            ])
            ->add('new_password', PasswordType::class, [
                'label' => 'label.new_password',
                'mapped' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver resolver
     *
     * @return void return
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
