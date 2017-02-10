<?php

namespace Viteloge\FrontendBundle\Form\Type {

    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormInterface;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormEvent;
    use Symfony\Component\Form\FormEvents;
    use Doctrine\ORM\EntityRepository;
    use Doctrine\ORM\EntityManager;
    use Viteloge\InseeBundle\Entity\InseeCity;
    use Viteloge\CoreBundle\Entity\Api;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;

    class ApiType extends AbstractType {

        protected $em;

        public function __construct(EntityManager $em) {
            $this->em = $em;
        }

        public function buildForm(FormBuilderInterface $builder, array $options) {
            $builder
                ->add('inseeCity', TextType::class, array(
                    'label' => 'usersearch.inseecity',
                    'data_class' => 'Viteloge\InseeBundle\Entity\InseeCity',
                    'required' => true,
                    'empty_data' => null
                ))
                ->add('send', SubmitType::class);

            $formModifier = function (FormInterface $form, /*InseeCity*/ $inseeCity) {
                $choices = (empty($inseeCity)) ? array() : array($inseeCity);
                $form->add('inseeCity', EntityType::class, array(
                    'class' => 'VitelogeInseeBundle:InseeCity',
                    'data_class' => null,
                    'choice_label' => 'getNameAndPostalcode',
                    'group_by' => 'inseeDepartment',
                    'label' => 'usersearch.inseecity',
                    'choices' => $choices,
                    'expanded' => false,
                    'multiple' => false,
                    'data' => $inseeCity, // not really necessary
                    'required' => true,
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
                function (FormEvent $event) use ($formModifier) {
                    $form = $event->getForm();
                    $data = $event->getData();
                    if ($form->has('inseeCity')) {
                        $inseeCity = $form->get('inseeCity')->getData();
                        if (!($inseeCity instanceof InseeCity) || $inseeCity->getId() != $data['inseeCity']) {
                            $inseeCity = $this->em->getRepository('VitelogeInseeBundle:InseeCity')->findOneById($data['inseeCity']);
                            $formModifier($form, $inseeCity);
                        }
                    }
                }
            );
        }

        public function configureOptions(OptionsResolver $resolver){
            $resolver->setDefaults(array(
                'data_class' => Api::class,
                'csrf_protection' => true,
                'csrf_field_name' => '_token',
                'csrf_token_id'   => 'task_item'
            ));
        }

        public function getBlockPrefix() {
            return 'viteloge_frontend_api';
        }

    }

}
