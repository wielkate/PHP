<?php
/**
 * Tag form.
 */

namespace App\Form;

use App\Entity\Song;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * class TagType.
 */
class TagType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder param
     * @param array                $options param
     *
     * @return void return
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('slug')
            ->add('songs', EntityType::class, [
                'class' => Song::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver param
     *
     * @return void return
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'tag';
    }
}
