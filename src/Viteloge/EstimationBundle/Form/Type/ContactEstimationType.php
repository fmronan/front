<?php

namespace Viteloge\EstimationBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ContactEstimationType extends MyTypeWithBoolean
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'lastname',
                TextType::class,
                array(
                    'label' => 'estimation.label.nom',
                    'constraints' => array(
                        new Assert\NotBlank()
                    )
                )
            )
            ->add(
                'firstname',
                TextType::class,
                array(
                    'label' => 'estimation.label.prenom',
                    'constraints' => array(
                        new Assert\NotBlank()
                    )
                )
            )
            ->add(
                'phone',
                TextType::class,
                array(
                    'label' => 'estimation.label.tel',
                    'constraints' => array(
                        new Assert\NotBlank()
                    )
                )
            )
            ->add(
                'agencyRequest',
                ChoiceType::class,
                $this->makeBool( 'estimation.label.demande_agence', true )
            )
            ->add(
                'save',
                SubmitType::class,
                array('label' => 'estimation.label.contact_save')
            )
        ;

    }
    public function getBlockPrefix()
    {
        return 'contact_estimation';
    }

}
