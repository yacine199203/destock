<?php

namespace App\Form;

use App\Entity\Characteristics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CharacteristicsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('characteristics',TextType::class,[
                'label'=>' ',
                'attr'=>[
                    'placeholder'=>'Ex: froid statique',
                ],
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Characteristics::class,
        ]);
    }
}
