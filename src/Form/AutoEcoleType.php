<?php

namespace App\Form;

use App\Entity\AutoEcole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoEcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomAutoEcole')
            ->add('telAutoEcole')
            ->add('siretAutoEcole')
            ->add('imageAutoEcole')
            ->add('lienWebAutoEcole')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AutoEcole::class,
        ]);
    }
}
