<?php

namespace Viteloge\UserBundle\Form\Type {

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;

    class RegistrationFormType extends AbstractType {

        public function buildForm(FormBuilderInterface $builder, array $options) {
            $builder->remove('username');
        }

        public function getParent() {
            return 'FOS\UserBundle\Form\Type\RegistrationFormType';
        }

        public function getName() {
            return $this->getBlockPrefix();
        }

        public function getBlockPrefix(){

       return 'viteloge_user_registration';
        }
    }

}
