<?php

namespace Viteloge\FrontendBundle\Form\Type {

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Viteloge\CoreBundle\Component\Enum\SubjectEnum;
    use Viteloge\CoreBundle\Entity\Contact;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;

    class ContactType extends AbstractType {
        const __default = null;

        const NATIONAL_AD = 1;

        const LOCAL_AD = 2;

        const HIGHLIGHT_AD = 3;

        const PARTNER = 4;

        const BUG = 5;

        const TECHNICAL_ASSIST = 6;

        const MISC_QUESTION = 7;

        public function buildForm(FormBuilderInterface $builder, array $options) {
            $subjectEnum = new SubjectEnum();
            $builder
                ->add('firstname',TextType::class)
                ->add('lastname',TextType::class)
                ->add('company',TextType::class)
                ->add('phone',TextType::class)
                ->add('email',EmailType::class)
                ->add('message', TextareaType::class)
                ->add('subject', ChoiceType::class, array(
                    'choices' => $subjectEnum->choices(),
                    'expanded' => false,
                    'multiple' => false,
                    'required' => true,
                    //'empty_value' => 'contact.empty_value',
                    'preferred_choices' => array()
                ))
                ->add('address', TextareaType::class)
                ->add('postalCode',TextType::class)
                ->add('city',TextType::class)
                ->add('send', SubmitType::class);
        }

        public function configureOptions(OptionsResolver $resolver){
            $resolver->setDefaults(array(
                'data_class' => Contact::class,
                'csrf_protection' => true,
                'csrf_field_name' => '_token',
                'csrf_token_id'   => 'task_item'
            ));
        }

        public function getBlockPrefix() {
            return 'viteloge_frontend_contact';
        }

    }

}
