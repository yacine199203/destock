<?php

namespace App\Form;

use App\Entity\Recrut;
use App\Form\ProfilRecrutType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RecrutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('poste',TextType::class,[
                'label'=>'Poste :',
                'attr'=>[
                    'placeholder'=>'Ex: Commercial'    
                ]
            ])
            ->add('type',ChoiceType::class,[
                'choices' => [
                   'CDI' => 'CDI',
                   'CDD' => 'CDD',
                ]
            ])
            ->add('city',TextType::class,[
                'label'=>'Ville :',
                'attr'=>[
                    'placeholder'=>'Ex: Alger'    
                ]
            ])
            ->add('profilRecruts',CollectionType::class,
            [
                'label'=>'Profil recherché :',
                'entry_type' => ProfilRecrutType::class,
                'allow_add' => true,
                'allow_delete' => true,
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
            'data_class' => Recrut::class,
        ]);
    }
}
