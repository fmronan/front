<?php

namespace Viteloge\CoreBundle\Form\Type {

    //use Symfony\Component\OptionsResolver\OptionsResolverInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormInterface;
    use Symfony\Component\Form\FormTypeInterface;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormEvent;
    use Symfony\Component\Form\FormEvents;
    use Doctrine\ORM\EntityRepository;
    use Doctrine\ORM\EntityManager;
    use Acreat\InseeBundle\Entity\InseeCity;
    use Viteloge\CoreBundle\SearchEntity\AdSearch;
    use Viteloge\CoreBundle\Component\Enum\TransactionEnum;
    use Viteloge\CoreBundle\Component\Enum\TypeEnum;
    use Viteloge\CoreBundle\Component\Enum\RoomEnum;
    use Viteloge\CoreBundle\Component\Enum\DistanceEnum;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\MoneyType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;

    class AdSearchType extends AbstractType {

        private $em;

        public function __construct(EntityManager $em) {
            $this->em = $em;
        }

        /**
         * on devrait pouvoir virer choices_as_values en 3.0
         */
        public function buildForm(FormBuilderInterface $builder, array $options) {
            $transactionEnum = new TransactionEnum();
            $typeEnum = new TypeEnum();
            $roomEnum = new RoomEnum();
            $distanceEnum = new DistanceEnum();
            $builder
                ->add('transaction', ChoiceType::class, array(
                    'label' => 'ad.transaction',
                    'choices' => $transactionEnum->choices(),
                    'expanded' => false,
                    'multiple' => true,
                    'preferred_choices' => array(),
                ))
                ->add('where', ChoiceType::class, array(
                    'label' => 'ad.where',
                    'choices' => array(),
                    'expanded' => false,
                    'multiple' => true,
                    'preferred_choices' => array(),
                ))
                ->add('what', ChoiceType::class, array(
                    'label' => 'ad.what',
                    'choices' => $typeEnum->choices(),
                    'expanded' => false,
                    'multiple' => true,
                    'preferred_choices' => array(),
                ))
                ->add('rooms', ChoiceType::class, array(
                    'label' => 'ad.rooms',
                    'choices' => $roomEnum->choices(),
                    'expanded' => false,
                    'multiple' => true,
                    'preferred_choices' => array(),
                ))
             /*   ->add('keywords', 'text', array(
                    'label' => 'ad.keywords',
                    'required' => false
                ))*/
                ->add('minPrice', MoneyType::class, array(
                    'label' => 'ad.price.min',
                    'required' => false,
                    'scale' => 0
                ))
                ->add('maxPrice', MoneyType::class, array(
                    'label' => 'ad.price.max',
                    'required' => false,
                    'scale' => 0
                ))
             /*   ->add('radius', 'choice', array(
                    'label' => 'ad.radius',
                    'choices' => $distanceEnum->choices(),
                    'required' => false,
                    'empty_value' => false
                ))*/
                ->add('search', SubmitType::class)
            ;

            $em = $this->em;
            $formModifier = function (FormInterface $form, $cities) {
                $choices = (empty($cities)) ? array() : $cities;
                $form->add('where', EntityType::class, array(
                    'label' => 'ad.where',
                    'class' => 'VitelogeInseeBundle:InseeCity',
                    'data_class' => null,
                    'choice_label' => 'getNameAndPostalcode',
                    'group_by' => 'inseeDepartment',
                    'choices' => $choices,
                    'expanded' => false,
                    'multiple' => true,
                    'data' => $cities, // not really necessary
                    'required' => false,
                    //'preferred_choices' => array(),
                    'placeholder' => '',
                    'empty_data' => null,
                    'mapped' => true
                ));
            };

            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($formModifier, $em) {
                    $form = $event->getForm();
                    $data = $event->getData();
                    $where = $data->getWhere();
                    if (!empty($where)) {
                        $cities = $em->getRepository('VitelogeInseeBundle:InseeCity')->findById($where);
                        $formModifier($form, $cities);
                    }
                }
            );

            $builder->addEventListener(
                FormEvents::PRE_SUBMIT,
                function (FormEvent $event) use ($formModifier, $em) {
                    $form = $event->getForm();
                    $data = $event->getData();
                    if (isset($data['where'])) {
                        $cities = $em->getRepository('VitelogeInseeBundle:InseeCity')->findById($data['where']);
                        $formModifier(
                            $form,
                            $cities/*array_flip(array_map(
                                function($city){
                                    return $city->getId();
                                }, $cities
                            ))*/
                        );
                    }
                }
            );
        }
          //voir ici pour protection csrf
        public function configureOptions(OptionsResolver $resolver) {
           // parent::setDefaultOptions($resolver);
            $resolver->setDefaults(
                array(
                    'csrf_protection' => false,
                    //'csrf_protection' => true,
                    'data_class' => 'Viteloge\CoreBundle\SearchEntity\Ad',
                    'csrf_token_id' => 'task_form',
                )
            );
        }

        public function getName() {
            return $this->getBlockPrefix();
        }

        public function getBlockPrefix()
       {
           return 'viteloge_core_adsearch';
       }

    }

}
