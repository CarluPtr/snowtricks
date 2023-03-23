<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Figure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FigureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                    'label' => 'Nom',
                ])
            ->add('description')
            ->add('content', TextareaType::class, [
                'attr' => ['style'=>'overflow:hidden; height:200px'],
                'label' => 'Contenu',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie'
            ])
            ->add('video', TextType::class, [
                'label' => 'Youtube Video',
                'label_attr' => array(
                    'class' => 'col-sm-6'
                )
            ])
            ->add('images', CollectionType::class,array(
                'label' => 'Images',
                'entry_type' => ImageFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype'     => true,
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
