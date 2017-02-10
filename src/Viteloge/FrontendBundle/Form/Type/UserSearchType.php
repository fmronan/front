<?php

namespace Viteloge\FrontendBundle\Form\Type {

    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormInterface;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormEvent;
    use Symfony\Component\Form\FormEvents;
    use Doctrine\ORM\EntityRepository;
    use Doctrine\ORM\EntityManager;
    use Viteloge\CoreBundle\Entity\UserSearch;
    use Viteloge\InseeBundle\Entity\InseeCity;
    use Viteloge\CoreBundle\Component\Enum\TransactionEnum;
    use Viteloge\CoreBundle\Component\Enum\TypeEnum;
    use Viteloge\CoreBundle\Component\Enum\RoomEnum;
    use Viteloge\CoreBundle\Component\Enum\DistanceEnum;
    use Viteloge\CoreBundle\Component\Enum\UserSearchSourceEnum;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\MoneyType;
    use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;


    class UserSearchType extends AbstractType {

        private $tokenStorage;

        private $em;

        public function __construct(TokenStorageInterface $tokenStorage, EntityManager $em) {
            $this->tokenStorage = $tokenStorage;
            $this->em = $em;
        }

        public function buildForm(FormBuilderInterface $builder, array $options) {
            $transactionEnum = new TransactionEnum();
            $typeEnum = new TypeEnum();
            $roomEnum = new RoomEnum();
            $distanceEnum = new DistanceEnum();
            $builder
                ->add('transaction', ChoiceType::class, array(
                    'label' => 'usersearch.transaction',
                    'choices' => $transactionEnum->choices(),
                    'expanded' => true,
                    'multiple' => false,
                    'preferred_choices' => array()
                ))
                ->add('inseeCity', TextType::class, array(
                    'label' => 'usersearch.inseecity',
                    'data_class' => 'Viteloge\InseeBundle\Entity\InseeCity',
                    'required' => true,
                    'empty_data' => null
                ))
                ->add('type', ChoiceType::class, array(
                    'label' => 'usersearch.type',
                    'choices' => $typeEnum->choices(),
                    'expanded' => false,
                    'multiple' => true,
                    'preferred_choices' => array(),
                ))
                ->add('rooms', ChoiceType::class, array(
                    'label' => 'usersearch.rooms',
                    'choices' => $roomEnum->choices(),
                    'expanded' => false,
                    'multiple' => true,
                    'required' => false,
                    'preferred_choices' => array(),
                    'placeholder' => 'usersearch.empty_value'
                ))
                ->add('keywords', TextType::class, array(
                    'label' => 'usersearch.keywords',
                    'required' => false
                ))
                ->add('budgetMin', MoneyType::class, array(
                    'label' => 'usersearch.budgetMin',
                    'required' => false,
                    'scale' => 0
                ))
                ->add('budgetMax', MoneyType::class, array(
                    'label' => 'usersearch.budgetMax',
                    'required' => false,
                    'scale' => 0
                ))
                ->add( 'radius', ChoiceType::class, array(
                    'label' => 'usersearch.radius',
                    'choices' => $distanceEnum->choices(),
                    'required' => true
                ))
                ->add( 'helpEnabled', null, array(
                    'label' => 'usersearch.help',
                    'required' => false
                ))
                ->add('mailEnabled', CheckboxType::class, array(
                    'label' => 'usersearch.mailenabled',
                    'required' => false
                ))
                ->add('save', SubmitType::class)
            ;

            // grab the user, do a quick sanity check that one exists
            $user = $this->tokenStorage->getToken()->getUser();
            if (!$user) {
                throw new \LogicException(
                    'The UserSearchType cannot be used without an authenticated user!'
                );
            }

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

            $builder->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($user) {
                    $data = $event->getForm()->getData();
                    $data->setCivility($user->getCivility());
                    $data->setLastname($user->getLastname());
                    $data->setFirstname($user->getFirstname());
                    $data->setMail($user->getEmail());
                    $data->setSource(UserSearchSourceEnum::WEB);

                    $deletedAt = $data->getDeletedAt();
                    $isMailEnabled = $data->isMailEnabled();
                    if ($isMailEnabled) {
                        $data->setDeletedAt(null);
                    }
                    elseif (!$isMailEnabled && empty($deletedAt)) {
                        $data->setDeletedAt(new \DateTime('now'));
                    }
                }
            );

        }

        public function configureOptions(OptionsResolver $resolver){
            $resolver->setDefaults(array(
                'data_class' => UserSearch::class,
                'csrf_protection' => true,
                'csrf_field_name' => '_token',
                'csrf_token_id'   => 'task_item'
            ));
        }

        public function getBlockPrefix() {
            return 'viteloge_frontend_usersearch';
        }

    }

}
