<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', FileType::class, [
                'attr' => [
                    'accept' => 'image/*',
                ],
                'data_class' => null,
                'required' => false,
                //'by_reference' => false,
                'label' => "Image"
            ]
            );
        $builder->get('name')->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            if (null === $event->getData()) {
                $event->setData($event->getForm()->getData());
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
