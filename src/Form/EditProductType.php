<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EditProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('productName',TextType::class,[
            'label'=>'Produit :',
            'attr'=>[
                'placeholder'=>'Ex: Sigma'    
            ]
        ])
        ->add('category',EntityType::class,[
            'label'=>'Catégorie :',
            'class'=>'App\Entity\Category',
            'choice_label'=>'categoryName',
            'choice_value'=>'id',
            'expanded'=>false,
            'multiple'=>false,
        ])
        ->add('png',FileType::class,[
            'label'=>'Image de présentation (uniquement en format png) :',
            'required' => false,
            'mapped'=> false,
            'attr'=>[
                'accept'=>'.png', 
                'placeholder'=>'Ex: image.png', 
            ]
        ])
        ->add('pdf',FileType::class,[
            'label'=>'Fiche téchnique (uniquement en format pdf) :',
            'required' => false,
            'mapped'=> false,
            'attr'=>[
                'accept'=>'.pdf', 
                'placeholder'=>'Ex: fichier.pdf', 
            ]
        ])
        ->add('image',FileType::class,[
            'label'=>'Images (uniquement en format png) :',
            'attr'=>[
                'accept'=>'.png', 
                'placeholder'=>'Ex: images.png', 
            ],
            'mapped'=> false,
            'required'=> false,
            'multiple'=> true,
        ])
        ->add('jobProducts',EntityType::class,[
            'label'=>'Métiers :',
            'class'=>'App\Entity\Job',
            'choice_label'=>'jobName',
            'choice_value'=>'id',
            'mapped'=>false,
            'expanded'=>false,
            'multiple'=>false,
        ])
        ->add('prices',CollectionType::class,
        [
            'label'=>'Dimensions et prix TTC:',
            'entry_type' => PriceType::class,
            'allow_add' => true,
            'allow_delete' => true,
        ])
        ->add('characteristics',CollectionType::class,
            [
                'label'=>'Caractéristiques :',
                'entry_type' => CharacteristicsType::class,
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
            'data_class' => Product::class,
        ]);
    }
}
