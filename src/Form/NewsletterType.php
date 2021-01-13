<?php

namespace App\Form;

use App\Entity\Newsletter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name',TextType::class,[
            'label'=>false,
            'attr'=>[
                'placeholder'=>'Votre nom / société'    
            ]
        ])
        ->add('email',EmailType::class,[
            'label'=>false,
            'attr'=>[
                'placeholder'=>'Votre email'    
            ]
        ])
        ->add('description',HiddenType::class,[
            'mapped'=> false,
            'required'=> false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Newsletter::class,
        ]);
    }
}
