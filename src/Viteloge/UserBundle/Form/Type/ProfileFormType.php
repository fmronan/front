<?php

namespace Viteloge\UserBundle\Form\Type {

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Viteloge\CoreBundle\Component\Enum\CivilityEnum;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\NumberType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

    class ProfileFormType extends AbstractType {

        public function buildForm(FormBuilderInterface $builder, array $options) {
            $civility = new CivilityEnum();
            $builder
                ->add('civility', ChoiceType::class, array(
                    'label' => 'profile.civility',
                    'choices' => $civility->choices(),
                    'expanded' => false,
                    'multiple' => false,
                    'placeholder' => 'profile.civility'
                ))
                ->add('firstname', TextType::class, array(
                    'label' => 'profile.firstname'
                ))
                ->add('lastname', TextType::class, array(
                    'label' => 'profile.lastname'
                ))
                ->add('email', EmailType::class, array(
                    'label' => 'profile.email'
                ))
                ->add('phone', TextType::class, array(
                    'label' => 'profile.phone',
                   // 'pattern' => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
                ))
                ->add('partnerContactEnabled', CheckboxType::class, array(
                    'label' => 'profile.partnercontactenabled'
                ))
                ->remove('username')
                ->remove('current_password');
        }

        public function configureOptions(OptionsResolver $resolver) {
            $resolver->setDefaults(array(
                'translation_domain'  => 'FOSUserBundle'
            ));
        }

        public function getParent() {
            return 'FOS\UserBundle\Form\Type\ProfileFormType';
        }

        public function getBlockPrefix() {
            return 'viteloge_user_profile';
        }
    }

}
