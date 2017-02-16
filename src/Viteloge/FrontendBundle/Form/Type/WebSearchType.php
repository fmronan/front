<?php

namespace Viteloge\FrontendBundle\Form\Type {

    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
    use Symfony\Component\EventDispatcher\EventDispatcher;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormEvents;
    use Symfony\Component\Form\FormEvent;
    use Doctrine\ORM\EntityManager;
    use Viteloge\CoreBundle\Entity\WebSearch;
    use Viteloge\InseeBundle\Entity\InseeCity;
    use Viteloge\CoreBundle\Component\Enum\UserSearchSourceEnum;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Viteloge\FrontendBundle\Form\Type\UserSearchType;

    class WebSearchType extends AbstractType {

        private $tokenStorage;


        public function __construct(TokenStorageInterface $tokenStorage, EntityManager $em) {
            $this->tokenStorage = $tokenStorage;
        }

        public function buildForm(FormBuilderInterface $builder, array $options) {
            $builder->add('title', TextType::class, array(
                'label' => 'websearch.title'
            ));
            $builder->add('userSearch', UserSearchType::class);

            // grab the user, do a quick sanity check that one exists
            $user = $this->tokenStorage->getToken()->getUser();
            if (!$user) {
                throw new \LogicException(
                    'The WebSearchType cannot be used without an authenticated user!'
                );
            }

            $builder->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($user) {
                    $data = $event->getForm()->getData();
                    $data->setUser($user);
                }
            );
        }

        public function configureOptions(OptionsResolver $resolver){
            $resolver->setDefaults(array(
                'data_class' => WebSearch::class,
                'csrf_protection' => true,
                'csrf_field_name' => '_token',
                'csrf_token_id'   => 'task_item'
            ));
        }

        public function getBlockPrefix() {
            return 'viteloge_frontend_websearch';
        }

    }

}
