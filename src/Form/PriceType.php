<?php

namespace App\Form;

use App\Entity\Price;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dimension',IntegerType::class,[
                'label'=>'Dimension :',
                'attr'=>[ 
                    'placeholder'=>'Dimension', 
                ],
            ])
            ->add('price1',NumberType::class,[
                'label'=>'Prix public :',
                'attr'=>[ 
                    'placeholder'=>'Prix 1', 
                ],
            ])
            ->add('price2',NumberType::class,[
                'label'=>'Prix revendeur :',
                'attr'=>[ 
                    'placeholder'=>'Prix 2', 
                ],
            ])
            ->add('price3',NumberType::class,[
                'label'=>'Prix distributeur :',
                'attr'=>[ 
                    'placeholder'=>'Prix 3', 
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Price::class,
        ]);
    }
}
