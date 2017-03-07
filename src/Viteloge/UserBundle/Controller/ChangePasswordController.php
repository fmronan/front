<?php

namespace Viteloge\UserBundle\Controller {

    use FOS\UserBundle\Event\FilterUserResponseEvent;
    use FOS\UserBundle\Event\FormEvent;
    use FOS\UserBundle\Event\GetResponseUserEvent;
    use FOS\UserBundle\Form\Factory\FactoryInterface;
    use FOS\UserBundle\FOSUserEvents;
    use FOS\UserBundle\Model\UserInterface;
    use FOS\UserBundle\Model\UserManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\EventDispatcher\EventDispatcherInterface;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Security\Core\Exception\AccessDeniedException;
    use FOS\UserBundle\Controller\ChangePasswordController as BaseController;

    class ChangePasswordController extends BaseController {

        /**
         * Change user password
         */
        public function changePasswordAction(Request $request) {
            $translated = $this->get('translator');

            // SEO
            $canonicalLink = $this->get('router')->generate($request->get('_route'), array(), true);
            $seoPage = $this->container->get('sonata.seo.page');
            $seoPage
                ->setTitle($translated->trans('viteloge.user.changepassword.changepassword.title'))
                ->addMeta('name', 'robots', 'noindex, nofollow')
                ->addMeta('name', 'description', $translated->trans('viteloge.user.changepassword.changepassword.description'))
                ->addMeta('property', 'og:title', $translated->trans('viteloge.user.changepassword.changepassword.title'))
                ->addMeta('property', 'og:type', 'website')
                ->addMeta('property', 'og:url',  $canonicalLink)
                ->addMeta('property', 'og:description', $translated->trans('viteloge.user.changepassword.changepassword.description'))
                ->setLinkCanonical($canonicalLink)
            ;
            // -- ici ce qui est dessous c'est pour tester l'ab testing si pas besoin on utilise le parent::
          return parent::changePasswordAction($request);
    /*        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $formFactory = $this->get('fos_user.change_password.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }
        //pour ab testing on verifie si le cookies existe il va renvoyer UsersBundle ou UserBundle

        $template_active = $this->get('viteloge_user_verif.ab.cookies')->abVerif();

        if($template_active == 'UsersBundle'){
            return $this->render('VitelogeUsersBundle:ChangePassword:change_password.html.twig', array(
                        'form' => $form->createView(),
                    ));
        }else{
            return $this->render('@FOSUser/ChangePassword/change_password.html.twig', array(
                        'form' => $form->createView(),
                    ));
        }

*/
        }

    }

}

