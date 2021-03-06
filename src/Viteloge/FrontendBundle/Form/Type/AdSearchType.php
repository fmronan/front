<?php

namespace Viteloge\FrontendBundle\Form\Type {

    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormInterface;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormEvent;
    use Symfony\Component\Form\FormEvents;
    use Doctrine\ORM\EntityManager;
    use Viteloge\CoreBundle\SearchEntity\Ad;
    use Viteloge\CoreBundle\Component\Enum\TransactionEnum;
    use Viteloge\CoreBundle\Component\Enum\TypeEnum;
    use Viteloge\CoreBundle\Component\Enum\RoomEnum;
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

        public function configureOptions(OptionsResolver $resolver){
            $resolver->setDefaults(array(
                'data_class' => Ad::class,
                'csrf_protection' => true,
                'csrf_field_name' => '_token',
                'csrf_token_id'   => 'task_item'
            ));
        }

        public function getBlockPrefix()
       {
           return 'viteloge_core_adsearch';
       }

    }

}
