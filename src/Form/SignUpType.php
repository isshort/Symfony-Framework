<?php

namespace App\Form;

use App\Entity\Drink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class SignUpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'attr'=>[
                    'placeholder'=>'Enter your name',
                    'class'=>'custom-class'
                ]
            ])
            ->add('last_name',TextType::class,[
                'attr'=>[
                    'placeholder'=>'Enter your last name'
                ]
                ])
            ->add('year',ChoiceType::class,[
                'choices'=>[
                    'choices' => $this->getYears(1960),
                ]
            ])
//            ->add('is_active',RadioType::class,[
//                'radio'=>[
//                    'active'=>true,
//                    'deactive'=>false,
//                ]
//            ])
            ->add('save',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-success'
                ]
            ])
        ;
    }
    private function getYears($min, $max='current')
    {
        $years = range($min, ($max === 'current' ? date('Y') : $max));

        return array_combine($years, $years);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Drink::class,
        ]);
    }
}
