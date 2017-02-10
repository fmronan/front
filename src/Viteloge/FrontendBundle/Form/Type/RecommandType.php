<?php

namespace Viteloge\FrontendBundle\Form\Type {

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormEvent;
    use Symfony\Component\Form\FormEvents;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Viteloge\CoreBundle\Entity\Recommand;
    use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
    use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
    use Viteloge\CoreBundle\Component\Enum\SubjectEnum;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\CollectionType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;

    class RecommandType extends AbstractType {

        public function buildForm(FormBuilderInterface $builder, array $options) {
            $subjectEnum = new SubjectEnum();
            $builder
                ->add('firstname')
                ->add('lastname')
                ->add('email')
                ->add('message', TextareaType::class)
                ->add('emails', CollectionType::class, array(
                    'entry_type'   => EmailType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype' => true,
                    'entry_options'  => array(
                        'required'  => false,
                        'attr'      => array(
                            'class' => ''
                        )
                    ),
                ))
                ->add('recaptcha', EWZRecaptchaType::class, array(
                    'attr' => array(
                        'options' => array(
                            'theme' => 'light',
                            'type'  => 'image',
                            'size'  => 'normal'
                        )
                    ),
                    'mapped'      => false,
                    'constraints' => array(
                        new IsTrue()
                    )
                ))
                ->add('send', SubmitType::class);

            $builder->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $data = $event->getForm()->getData();
                    $emails = $data->getEmails();
                    if ($emails->isEmpty()) {
                        $data->addEmail('');
                    }
                }
            );
        }

        public function configureOptions(OptionsResolver $resolver){
            $resolver->setDefaults(array(
                'data_class' => Recommand::class,
                'csrf_protection' => true,
                'csrf_field_name' => '_token',
                'csrf_token_id'   => 'task_item'
            ));
        }

        public function getBlockPrefix() {
            return 'viteloge_frontend_recommand';
        }

    }

}
