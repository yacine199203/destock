<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstName',TextType::class,[
            'label'=>'Prénom :',
            'attr'=>[
                'placeholder'=>'Prénom'    
            ]
        ])
        ->add('lastName',TextType::class,[
            'label'=>'Nom :',
            'attr'=>[
                'placeholder'=>'Nom'    
            ]
        ])
        ->add('adress',TextType::class,[
            'label'=>'Adresse :',
            'attr'=>[
                'placeholder'=>'adresse'    
            ]
        ])
        ->add('city',ChoiceType::class,[
            'choices' => [
               'Adrar' => 'Adrar',
               'Chlef' => 'Chlef',
               'Laghouat' => 'Laghouat',
               'Oum El Bouaghi' => 'Oum El Bouaghi',
               'Batna' => 'Batna',
               'Béjaïa' => 'Béjaïa',
               'Biskra' => 'Biskra',
               'Béchar' => 'Béchar',
               'Blida' => 'Blida',
               'Bouira' => 'Bouira',
               'Tamanrasset' => 'Tamanrasset',
               'Tébessa' => 'Tébessa',
               'Tlemcen' => 'Tlemcen',
               'Tiaret' => 'Tiaret',
               'Tizi Ouzou' => 'Tizi Ouzou',
               'Alger' => 'Alger',
               'Djelfa' => 'Djelfa',
               'Jijel' => 'Jijel',
               'Sétif' => 'Sétif',
               'Saïda' => 'Saïda',
               'Skikda' => 'Skikda',
               'Sidi Bel Abbès' => 'Sidi Bel Abbès',
               'Annaba' => 'Annaba',
               'Guelma' => 'Guelma',
               'Constantine' => 'Constantine',
               'Médéa' => 'Médéa',
               'Mostaganem' => 'Mostaganem',
               'M\'Sila' => 'M\'Sila',
               'Mascara' => 'Mascara',
               'Ouargla' => 'Ouargla',
               'Oran' => 'Oran',
               'El Bayadh' => 'El Bayadh',
               'Illizi' => 'Illizi',
               'BBA' => 'BBA',
               'Boumerdès' => 'Boumerdès',
               'Tarf' => 'Tarf',
               'Tindouf' => 'Tindouf',
               'Tissemsilt' => 'Tissemsilt',
               'El Oued' => 'El Oued',
               'Khenchela' => 'Khenchela',
               'Souk Ahras' => 'Souk Ahras',
               'Tipaza' => 'Tipaza',
               'Mila' => 'Mila',
               'Aïn Defla' => 'Aïn Defla',
               'Naâma' => 'Naâma',
               'Aïn Témouchent' => 'Aïn Témouchent',
               'Ghardaïa' => 'Ghardaïa',
               'Relizane' => 'Relizane',
                ]
            ])
        ->add('phone',NumberType::class,[
            'label'=>'Téléphone :',
            'attr'=>[
                'placeholder'=>'tél'    
            ],
            'required'=> false,
        ])
        ->add('email',EmailType::class,[
            'label'=>'Email :',
            'attr'=>[
                'placeholder'=>'exemple@email.com'    
            ]
        ])
        ->add('roles',ChoiceType::class,[
            'choices' => [
               'Administrateur' => 'ROLE_ADMIN',
               'Commercial' => 'ROLE_SELLER',
               'Dépositaire' => 'ROLE_DISTRIBUTOR',
               'Revendeur' => 'ROLE_DEALER',
            ],
            'expanded' => true,
            'multiple' => true,
            'label' => 'Rôles :'
        ])
        ->add('description',HiddenType::class,[
            'mapped'=> false,
            'required'=> false,
        ])
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
