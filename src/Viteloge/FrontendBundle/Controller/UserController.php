<?php

namespace Viteloge\FrontendBundle\Controller {

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Security\Core\Exception\AccessDeniedException;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use FOS\UserBundle\FOSUserEvents;
    use FOS\UserBundle\Event\GetResponseUserEvent;
    use Viteloge\CoreBundle\Entity\User;
    use Viteloge\CoreBundle\SearchEntity\Ad as AdSearch;
    use Viteloge\FrontendBundle\Form\Type\AdSearchType;

    /**
     * @Route("/user")
     */
    class UserController extends Controller {

        /**
         * @Route("/")
         * @Method({"GET"})
         * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
         * @Template("VitelogeFrontendBundle:User:index.html.twig")
         */
        public function indexAction( Request $request ) {
            $translated = $this->get('translator');

            // Breadcrumbs
            $this->get('viteloge_frontend_generate.breadcrump')->genereBreadcrump(array('last'=>'breadcrumb.user'));
            // --

            // SEO
            $canonicalLink = $this->get('router')->generate(
                $request->get('_route'),
                $request->get('_route_params'),
                true
            );
            $this->container->get('viteloge_frontend_generate.seo')->genereSeo('noindex, nofollow',$translated->trans('viteloge.frontend.user.index.title'),$translated->trans('viteloge.frontend.user.index.description'),$canonicalLink);
            // --
            $adSearch = new AdSearch();
            $adSearch->handleRequest($request);
            $form = $this->createForm(AdSearchType::class, $adSearch);

            return array(
                'form' => $form->createView(),
            );
        }

        /**
         * @Route("/registerModal")
         * @Method({"GET"})
         * @see RegisterController::registerAction
         * @Template("VitelogeFrontendBundle:User:registermodal.html.twig")
         */
        public function registerModalAction(Request $request) {
            $formFactory = $this->get('fos_user.registration.form.factory');
            $userManager = $this->get('fos_user.user_manager');
            $dispatcher = $this->get('event_dispatcher');

            $user = $userManager->createUser();
            $user->setEnabled(true);

            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $form = $formFactory->createForm();
            $form->setData($user);
            $form->handleRequest($request);

            return array(
                'form' => $form->createView(),
            );
        }

        /**
         * This is a legacy disabling fork
         * @Route(
         *     "/disableMail/{token}/{info}",
         *     name="viteloge_frontend_user_disablemail"
         * )
         * @Method({"GET", "POST"})
         */
        public function disableMailAction(Request $request, $token, $info) {
            $translated = $this->get('translator');
            $user = $this->getTokenAction($request, $token, $info);
            $user->setInternalMailDisabled(true);
            $userManager->updateUser($user);

            $this->addFlash(
                'success',
                $translated->trans('user.flash.internalmaildisabled')
            );

            return $this->redirectToRoute('viteloge_frontend_homepage');
        }

        /**
         * This is a legacy disabling fork
         * @Route(
         *     "/disablePartnerContact/{token}/{info}",
         *     name="viteloge_frontend_user_disablepartnercontact"
         * )
         * @Method({"GET", "POST"})
         */
        public function disablePartnerContactAction(Request $request, $token, $info) {
            $translated = $this->get('translator');
            $user = $this->getTokenAction($request, $token, $info);
            $user->setPartnerContactEnabled(false);
            $userManager->updateUser($user);

            $this->addFlash('success',$translated->trans('user.flash.partnercontactdisabled'));

            return $this->redirectToRoute('viteloge_frontend_homepage');
        }

        /**
        *
        *
        **/
        private function getTokenAction(Request $request, $token, $info){
            $data = json_decode( base64_decode( strtr( $info, '-_', '+/' ) ), true );

            if (empty($data) || empty($data['id'])) {
                throw new AccessDeniedException();
            }

            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserBy(array( 'id' => $data['id'] ));

            if (!$user instanceof User) {
                throw new AccessDeniedException();
            }

            $newTokenManager = $this->get('viteloge_frontend.mail_token_manager');
            $oldTokenManager = $this->get('viteloge_frontend.old_token_manager');
            $newTokenManager->setUser($user)->hash();
            $oldTokenManager->setUser($user)->hash();

            if (!$newTokenManager->isTokenValid($token) && !$oldTokenManager->isTokenValid($token)) {
                throw $this->createNotFoundException();
            }

            return $user;
        }

    }

}
