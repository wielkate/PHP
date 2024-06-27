<?php
/**
 * User type.
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType.
 */
class UserType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder builder
     * @param array<string, mixed> $options options
     *
     * @see FormTypeExtensionInterface::buildForm() form
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'email',
            TextType::class,
            [
                'label' => 'label.email',
                'required' => true,
                'attr' => ['max_length' => 64],
            ]
        );

        $builder->add(
            'password',
            PasswordType::class,
            [
                'label' => 'label.password',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]
        );

        $builder
            ->add('Roles', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Partner' => 'ROLE_PARTNER',
                    'Admin' => 'ROLE_ADMIN',
                ],
            ]);

        // Data transformer
        $builder->get('Roles')
            ->addModelTransformer(new CallbackTransformer(
                fn ($rolesArray) =>
                    // transform the array to a string
                    count($rolesArray) > 0 ? $rolesArray[0] : null,
                fn ($rolesString) =>
                    // transform the string back to an array
                    [$rolesString]
            ));
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string entity name
     */
    public function getBlockPrefix(): string
    {
        return 'user';
    }
}
