<?php

namespace Viteloge\FrontendBundle\Form\Type {

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Viteloge\CoreBundle\Entity\Message;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;

    class MessageType extends AbstractType {

        public function buildForm(FormBuilderInterface $builder, array $options) {
            $builder
                ->add('firstname', null, array(
                    'label' => 'message.firstname',
                ))
                ->add('lastname', null, array(
                    'label' => 'message.lastname'
                ))
                ->add('message', TextareaType::class, array(
                    'label' => 'message.message'
                ))
                ->add('email', null, array(
                    'label' => 'message.email'
                ))
                ->add('phone', null, array(
                    'label' => 'message.phone'
                ))
                ->add('send', SubmitType::class);
        }

        public function configureOptions(OptionsResolver $resolver){
            $resolver->setDefaults(array(
                'data_class' => Message::class,
                'csrf_protection' => true,
                'csrf_field_name' => '_token',
                'csrf_token_id'   => 'task_item'
            ));
        }

        public function getBlockPrefix() {
            return 'viteloge_frontend_message';
        }

    }

}
