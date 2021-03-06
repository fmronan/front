<?php

namespace Viteloge\FrontendBundle\Controller {

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Form\FormError;
    use Viteloge\CoreBundle\Entity\Contact;
    use Viteloge\FrontendBundle\Form\Type\ContactType;
    use Viteloge\CoreBundle\Entity\User;


    /**
     * Contact controller.
     *
     * @Route("/contact")
     */
    class ContactController extends Controller {

        /**
         * Creates a form to create a Message entity.
         *
         * @param Contact $contact The entity
         * @return \Symfony\Component\Form\Form The form
         */
        private function createCreateForm(Contact $contact) {
            return $this->createForm(
                ContactType::class,
                $contact,
                array(
                    'action' => $this->generateUrl('viteloge_frontend_contact_create'),
                    'method' => 'GET'
                )
            );
        }

        /**
         * Displays a form to create a new Contact entity.
         * Private cache for the user
         *
         * @Route(
         *      "/new",
         *      name="viteloge_frontend_contact_new"
         * )
         * @Cache(expires="tomorrow", public=false)
         * @Method("GET")
         * @Template("VitelogeFrontendBundle:Contact:new.html.twig")
         */
        public function newAction(Request $request) {
            $contact = new Contact();
            $contact->setUser($this->getUser());
            $form = $this->createCreateForm($contact);

            return array(
                'contact' => $contact,
                'form' => $form->createView(),
            );
        }

        /**
         * Creates a new Contact entity.
         * No cache for a post
         *
         * @Route(
         *      "/",
         *      name="viteloge_frontend_contact_create"
         * )
         * @Method({"GET","PUT"})
         * @Template("VitelogeFrontendBundle:Contact:new.html.twig")
         */
        public function createAction(Request $request) {

            $em = $this->getDoctrine()->getManager();
            $trans = $this->get('translator');
            $contact = new Contact();
            $contact->setUser($this->getUser());

            $form = $this->createCreateForm($contact);
            $form->handleRequest($request);

            if ($form->isValid()) {
                // afin de savoir si il faut envoyer un message pour inscription
                  $verifuser = $em->getRepository('VitelogeCoreBundle:User')->FindOneBy(array('email'=>$contact->getEmail()));
                    // log redirect
                        $contact->setIp($request->getClientIp());
                        $contact->setUa($request->headers->get('User-Agent'));
                        $em->persist($contact);
                        $em->flush();
                        $user = $em->getRepository('VitelogeCoreBundle:User')->FindOneBy(array('email'=>$contact->getEmail()));
                    if(empty($verifuser)){
                       $this->inscriptionMessage($user);
                     }

                     $this->sendMessage($contact);
                    return $this->redirect($this->generateUrl('viteloge_frontend_contact_success', array()));


            }else{
              $form->addError(new FormError($trans->trans('contact.send.error')));
            }

            return array(
                'contact' => $contact,
                'form' => $form->createView(),
            );
        }

         /**
         *
         */
        protected function inscriptionMessage(User $user) {
            $trans = $this->get('translator');
            $to = $user->getEmail();
            $mail = \Swift_Message::newInstance()
                ->setSubject($trans->trans('Votre compte sur viteloge.com'))
                ->setFrom('contact@viteloge.com')
                ->setTo($to)
                ->setBody(
                    $this->renderView(
                        'VitelogeFrontendBundle:Contact:Email/inscription.html.twig',
                        array(
                            'user' => $user
                        )
                    ),
                    'text/html'
                )
            ;
            return $this->get('mailer')->send($mail);
        }

        /**
         *
         */
        protected function sendMessage(Contact $contact) {
            $trans = $this->get('translator');
            $from = array(
                $contact->getEmail() => $contact->getFullname()
            );
            $mail = \Swift_Message::newInstance()
                ->setSubject($trans->trans('Demande de contact via le site Viteloge.com'))
                ->setFrom($from)
                ->setTo('contact@viteloge.com')
                ->setBody(
                    $this->renderView(
                        'VitelogeFrontendBundle:Contact:Email/contact.html.twig',
                        array(
                            'contact' => $contact
                        )
                    ),
                    'text/html'
                )
            ;
            return $this->get('mailer')->send($mail);
        }



        /**
         * Success contact
         * Private cache because of user informations
         *
         * @Route(
         *      "/success",
         *      name="viteloge_frontend_contact_success"
         * )
         * @Cache(expires="tomorrow", public=false)
         * @Method("GET")
         * @Template("VitelogeFrontendBundle:Contact:success.html.twig")
         */
        public function successAction() {
            $contact = null;
            return array(
                'contact' => $contact
            );
        }

        /**
         * Fail contact
         * Private cache because of user information
         *
         * @Route(
         *      "/fail",
         *      name="viteloge_frontend_contact_fail"
         * )
         * @Cache(expires="tomorrow", public=false)
         * @Method("GET")
         * @Template("VitelogeFrontendBundle:Contact:fail.html.twig")
         */
        public function failAction() {
            $contact = null;
            return array(
                'contact' => $contact
            );
        }

    }


}
