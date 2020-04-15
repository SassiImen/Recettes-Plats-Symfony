<?php

namespace App\Form;

use App\Entity\Score;
use App\Entity\Plat;
use App\Entity\Category;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PlatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('ingredients')
            ->add('tempsDeCuissant')
            ->add('score',EntityType::class,['class' => Score::class,
            'choice_label' => 'poucentage',  
            'label' => 'Score'])
            ->add('category',EntityType::class,['class' => Category::class,
            'choice_label' => 'titre',
            'label' => 'Catégorie']);}

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Plat::class,
        ]);
    }
}
