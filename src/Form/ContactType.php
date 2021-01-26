<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'label'=>'Nom :',
                'required' => false,
                'attr'=>[
                    'placeholder'=>'Nom'    
                ]
            ])
            ->add('email',EmailType::class,[
                'label'=>'Email :',
                'required' => false,
                'required' => false,
                'attr'=>[
                    'placeholder'=>'exemple@email.com'    
                ]
            ])
            ->add('file',FileType::class,[
                'label'=>'fichier :',
                'required' => false,
                'mapped'=> false,
                'attr'=>[ 
                    'placeholder'=>'Ex: image.png', 
                ]
            ])
            ->add('object',TextType::class,[
                'label'=>'Objet :',
                'required' => false,
                'attr'=>[
                    'placeholder'=>'Objet'    
                ]
            ])
            ->add('message', TextareaType::class ,[
                'label'=>'Message :',
                'required' => false,
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
            // Configure your form options here
        ]);
    }
}
