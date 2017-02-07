<?php

namespace Viteloge\FrontendBundle\EventListener {

    use Doctrine\ORM\Event\LifecycleEventArgs;
    use Symfony\Component\EventDispatcher\Event;
    use Viteloge\CoreBundle\Entity\Message;
    use Viteloge\CoreBundle\Entity\Contact;
    use Viteloge\CoreBundle\Entity\User;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
    use Doctrine\ORM\EntityManager;
    use Symfony\Component\DependencyInjection\ContainerInterface;



    /**
     * Listener called for create user with contact and message
     */
    class MessageListener {

       private $tokenStorage;
       private $em;
       private $container;

        /**
         *
         */
        public function __construct(ContainerInterface $container,TokenStorageInterface $tokenStorage, EntityManager $em) {
            $this->tokenStorage = $tokenStorage;
            $this->em = $em;
            $this->container = $container;

        }

        /**
         *
         */
        public function prePersist(LifecycleEventArgs $args) {
            $message = $args->getEntity();
            if ($message instanceof Message) {


            }
        }

    }

}
