<?php

namespace Viteloge\EstimationBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityManager;
use Viteloge\InseeBundle\Entity\InseeCity;
use Viteloge\CoreBundle\Entity\Estimate;
use Viteloge\EstimationBundle\Component\Enum\PathEnum;
use Viteloge\EstimationBundle\Component\Enum\TypeEnum;
use Viteloge\EstimationBundle\Component\Enum\ExpositionEnum;
use Viteloge\EstimationBundle\Component\Enum\ConditionEnum;
use Viteloge\EstimationBundle\Component\Enum\UsageEnum;
use Viteloge\EstimationBundle\Component\Enum\ApplicantEnum;
use Viteloge\EstimationBundle\Component\Enum\TimeEnum;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class EstimationType extends MyTypeWithBoolean {

    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $pathEnum = new PathEnum();
        $typeEnum = new TypeEnum();
        $expositionEnum = new ExpositionEnum();
        $conditionEnum = new ConditionEnum();
        $usageEnum = new UsageEnum();
        $applicantEnum = new ApplicantEnum();
        $timeEnum = new TimeEnum();

        $builder
            // general
            ->add(
                'numero',
                TextType::class,
                array( 'required' => false, 'attr' => array( 'placeholder' => 'estimation.label.numero' ) )
            )
         /*   ->add(
                'type_voie',
                ChosenType::class,
                array(
                    'choices' => $pathEnum->choices(),
                    'configs' => array( 'width' => '100%' ),
                    'label' => 'estimation.label.type_voie'
                )
            )*/
            ->add('type_voie', ChoiceType::class, array(
                    'label' => 'estimation.label.type_voie',
                    'choices' => $pathEnum->choices(),
                    'expanded' => false,
                    'multiple' => true,
                    'preferred_choices' => array(),
                ))
            ->add(
                'voie',
                TextType::class,
                array(
                    'required' => 'required',
                     'constraints' => array(
                        new Assert\NotBlank()
                     ),
                    'attr' => array( 'placeholder' => 'estimation.label.voie' ) )
            )
            ->add(
                'codepostal',
                TextType::class,
                array( 'attr' => array( 'placeholder' => 'estimation.label.codepostal') )
            )
            ->add('inseeCity', TextType::class, array(
                'label' => 'estimation.label.ville',
                'data_class' => 'Viteloge\InseeBundle\Entity\InseeCity',
                'required' => true,
                'empty_data' => null,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\NotNull()
                )
            ))
            // caracteristics
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'expanded' => true,
                    'choices' => $typeEnum->choices(),
                    'label' => 'estimation.label.type'
                )
            )
            ->add(
                'nb_pieces',
                IntegerType::class,
                array(
                    'label' => 'estimation.label.nb_pieces',
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Range( array( 'min' => 1, 'max' => 15 ) )
                    )
                )
            )
            ->add(
                'nb_sdb',
                IntegerType::class,
                array( 'required' => false, 'label' => 'estimation.label.nb_sdb' )
            )
            ->add(
                'surface_habitable',
                IntegerType::class,
                array(
                    'label' => 'estimation.label.surface_habitable',
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Range( array( 'min' => 1, 'max' => 5000 ) )
                    )
                )
            )
            ->add(
                'surface_terrain',
                IntegerType::class,
                array( 'required' => false, 'label' => 'estimation.label.surface_terrain' )  )
          /*  ->add(
                'exposition',
                ChosenType::class,
                array(
                    'choices' => $expositionEnum->choices(),
                    'required' => false,
                    'configs' => array( 'width' => '100%', 'minimumResultsForSearch' => -1 ),
                    'label' => 'estimation.label.exposition'
                )
            )*/
            ->add('exposition', ChoiceType::class, array(
                    'label' => 'estimation.label.exposition',
                    'choices' => $expositionEnum->choices(),
                    'expanded' => false,
                    'multiple' => true,
                    'preferred_choices' => array(),
                ))
            ->add(
                'etage',
                IntegerType::class,
                array( 'required' => false, 'label' => 'estimation.label.etage' )
            )
            ->add(
                'nb_etages',
                IntegerType::class, array( 'required' => false, 'label' => 'estimation.label.nb_etages' )
            )
            ->add(
                'nb_niveaux',
                IntegerType::class, array( 'required' => false, 'label' => 'estimation.label.nb_niveaux' )
            )
            ->add(
                'annee_construction',
                IntegerType::class, array( 'required' => false, 'label' => 'estimation.label.annee_construction' )
            )
            // details
            ->add(
                'ascenseur',
                ChoiceType::class,
                $this->makeBool( 'estimation.label.ascenseur' )
            )
            ->add(
                'balcon',
                ChoiceType::class,
                $this->makeBool( 'estimation.label.balcon' )
            )
            ->add(
                'terrasse',
                ChoiceType::class,
                $this->makeBool( 'estimation.label.terrasse' )
            )
            ->add(
                'parking',
                IntegerType::class,
                array( 'required' => false, 'label' => 'estimation.label.parking' )
            )
            ->add(
                'garage',
                IntegerType::class,
                array( 'required' => false, 'label' => 'estimation.label.garage' )
            )
            ->add(
                'vue',
                CheckboxType::class,
                array( 'required' => false, 'label' => 'estimation.label.vue' )
            )
            ->add(
                'vue_detail',
                TextType::class,
                array( 'required' => false, 'label' => 'estimation.label.vue_detail' )
            )
            // situation bien
         /*   ->add(
                'etat',
                ChosenType::class,
                array(
                    'choices' => $conditionEnum->choices(),
                    'required' => false,
                    'configs' => array( 'width' => '100%', 'minimumResultsForSearch' => -1 ),
                    'label' => 'estimation.label.etat'
                )
            )*/
            ->add('etat', ChoiceType::class, array(
                    'label' => 'estimation.label.etat',
                    'choices' => $conditionEnum->choices(),
                    'expanded' => false,
                    'multiple' => true,
                    'preferred_choices' => array(),
                ))
          /*  ->add(
                'usage',
                ChosenType::class,
                array(
                    'choices' => $usageEnum->choices(),
                    'required' => false,
                    'configs' => array( 'width' => '100%', 'minimumResultsForSearch' => -1 ),
                    'label' => 'estimation.label.usage'
                )
            )*/
            ->add('usage', ChoiceType::class, array(
                    'label' => 'estimation.label.usage',
                    'choices' => $usageEnum->choices(),
                    'expanded' => false,
                    'multiple' => true,
                    'preferred_choices' => array(),
                ))
            // situation proprio

           /* ->add(
                'proprietaire',
                ChosenType::class,
                array(
                    'choices' => $applicantEnum->choices(),
                    'configs' => array( 'width' => '100%', 'minimumResultsForSearch' => -1 ),
                    'label' => 'estimation.label.proprio',
                    'empty_value' => '',
                    'constraints' => array(
                        new Assert\NotBlank()
                    )
                )
            )*/
            ->add('proprietaire', ChoiceType::class, array(
                    'label' => 'estimation.label.proprietaire',
                    'choices' => $applicantEnum->choices(),
                    'expanded' => false,
                    'multiple' => true,
                    'preferred_choices' => array(),
                    'constraints' => array(
                        new Assert\NotBlank()
                    )
                ))
         /*   ->add(
                'delai',
                ChosenType::class,
                array(
                    'choices' => $timeEnum->choices(),
                    'required' => false,
                    'configs' => array( 'width' => '100%', 'minimumResultsForSearch' => -1 ),
                    'label' => 'estimation.label.delai'
                )
            )*/
            ->add('delai', ChoiceType::class, array(
                    'label' => 'estimation.label.delai',
                    'choices' => $timeEnum->choices(),
                    'expanded' => false,
                    'multiple' => true,
                    'preferred_choices' => array(),
                ))
            ->add(
                'agencyRequest',
                ChoiceType::class,
                $this->makeBool( 'estimation.label.demande_agence', true )
            )

            // owner details

            ->add(
                'mail',
                EmailType::class,
                array( 'required' => true, 'label' => 'estimation.label.mail' )
            )
            ->add(
                'save',
                SubmitType::class,
                array('label' => 'estimation.label.save')
            )
        ;

        $em = $this->em;
        $formModifier = function (FormInterface $form, /*InseeCity*/ $inseeCity) {
            $choices = (empty($inseeCity)) ? array() : array($inseeCity);
            $form->add('inseeCity', EntityType::class, array(
                'class' => 'VitelogeInseeBundle:InseeCity',
                'data_class' => null,
                //'property' => 'getNameAndPostalcode',
                'group_by' => 'inseeDepartment',
                'label' => 'estimation.label.ville',
                'choices' => $choices,
                'expanded' => false,
                'multiple' => false,
                'data' => $inseeCity, // not really necessary
                'required' => true,
                'placeholder' => '',
                'empty_data' => null,
                'mapped' => true
            ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $form = $event->getForm();
                $data = $event->getData();
                $inseeCity = ($data) ? $data->getInseeCity() : null;
                $formModifier($form, $inseeCity);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier, $em) {
                $form = $event->getForm();
                $data = $event->getData();
                if ($form->has('inseeCity')) {
                    $inseeCity = $form->get('inseeCity')->getData();
                    if (!($inseeCity instanceof InseeCity) || $inseeCity->getId() != $data['inseeCity']) {
                        $inseeCity = $em->getRepository('VitelogeInseeBundle:InseeCity')->findOneById($data['inseeCity']);
                        $formModifier($form, $inseeCity);
                    }
                }
            }
        );

        $builder->addEventListener( FormEvents::PRE_SET_DATA, array( $this, 'checkAndSetPersonalData' ) );
        $builder->addEventListener( FormEvents::PRE_SUBMIT, array( $this, 'checkAndSetPersonalData' ) );

    }

    public function getBlockPrefix() {
        return 'estimation';
    }

    public function checkAndSetPersonalData( FormEvent $event ) {
        $form = $event->getForm();
        $estimation = $event->getData();

        if ( is_array( $estimation ) ) {
            $constraints = 1 ==$estimation['agencyRequest'];
        } else {
            $constraints = false;
        }

        $form
            ->add(
                'lastname',
                TextType::class,
                array(
                    'label' => 'estimation.label.nom',
                    'disabled' => ! $constraints,
                    'constraints' => ($constraints ? array( new Assert\NotBlank() ) : array())
                )
            )
            ->add(
                'firstname',
                TextType::class,
                array(
                    'label' => 'estimation.label.prenom',
                    'disabled' => ! $constraints,
                    'constraints' =>( $constraints ? array( new Assert\NotBlank() ) : array())
                )
            )
            ->add(
                'phone',
                TextType::class,
                array(
                    'label' => 'estimation.label.tel',
                    'disabled' => ! $constraints,
                    'constraints' => $constraints ? array( new Assert\NotBlank() ) : array()
                )
            )
        ;
    }
}

