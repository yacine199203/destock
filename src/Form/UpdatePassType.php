<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UpdatePassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPass',PasswordType::class,[
                'label'=>'Ancien mot de passe :',
                'attr'=>[
                    'placeholder'=>'Mot de passe'    
                ]
            ])
            ->add('newPass',PasswordType::class,[
                'label'=>'Nouveau mot de passe :',
                'attr'=>[
                    'placeholder'=>'Mot de passe'    
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
