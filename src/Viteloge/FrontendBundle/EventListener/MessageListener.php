<?php

namespace Viteloge\FrontendBundle\EventListener {

    use Doctrine\ORM\Event\LifecycleEventArgs;
    use Symfony\Component\EventDispatcher\Event;
    use Viteloge\CoreBundle\Entity\Message;
    use Viteloge\CoreBundle\Entity\Contact;
    use Viteloge\CoreBundle\Entity\User;
    use Doctrine\ORM\EntityManager;
    use Symfony\Component\DependencyInjection\ContainerAwareInterface;
    use Symfony\Component\DependencyInjection\ContainerAwareTrait;



    /**
     * Listener called for create user with contact and message
     */
    class MessageListener implements ContainerAwareInterface{

       use ContainerAwareTrait;
       private $em;


        /**
         *
         */
        public function __construct(EntityManager $em) {
            $this->em = $em;
            $this->date = new \DateTime();
        }



        /**
         *
         */
        public function prePersist(LifecycleEventArgs $args) {
            $contact = $args->getEntity();

            if ($contact instanceof Message || $contact instanceof Contact) {
              $userManager = $this->container->get('fos_user.user_manager');
             // le findUserByEmail de fosuser ne fonctionne pas
               $user = $this->em->getRepository('VitelogeCoreBundle:User')->FindOneBy(array('email'=>$contact->getEmail()));

              if (null === $user) {
                $user = $userManager->createUser();
                $user->setUserName($contact->getEmail());
                $user->setPhone($contact->getPhone());
                $user->setEmail($contact->getEmail());
                $user->setEmailCanonical(strtolower($contact->getEmail()));

                $user->setFirstname($contact->getFirstname());

                $user->setLastname($contact->getLastname());
                $user->setPasswordRequestedAt($this->date);

                //on met à false il sera activé au clic sur lien d'un mail
                $user->setEnabled(false);
                $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());
                //pour conserver
                $user->setSalt($tokenGenerator->generateToken());
                $password = uniqid();
                $user->setPassword($this->container->get('security.encoder_factory')->getEncoder($user)->encodePassword($password,$user->getSalt()));

                $user->addRole('ROLE_USER');
                $userManager->updateUser($user);


              }

            }
        }

    }

}
