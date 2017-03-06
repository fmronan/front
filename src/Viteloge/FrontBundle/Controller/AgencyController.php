<?php

namespace Viteloge\FrontBundle\Controller {

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Symfony\Component\HttpFoundation\Request;
    use Viteloge\FrontendBundle\Controller\AgencyController as BaseController;

    /**
     * Note: this controller to have a short route name
     * @Route("/announcement")
     */
    class AgencyController extends BaseController {

        const DESCRIPTION_LENGHT = 150;

        /**
         * view the hosted page.No cache for this page
         *
         *
         * @Route(
         *     "/{id}/{description}",
         *     requirements={
         *
         *     },
         *     name="viteloge_frontend_agency_view"
         * )
         * @Route(
         *     "/favourite/{id}/{description}",
         *     requirements={
         *
         *     },
         *     name="viteloge_frontend_favourite_view"
         * )
         * @Route(
         *     "/home/{id}/{description}",
         *     requirements={
         *
         *     },
         *     name="viteloge_frontend_agency_home"
         * )
         * @Route(
         *     "/lastview/{id}/{description}",
         *     requirements={
         *
         *     },
         *     name="viteloge_frontend_agency_lastview"
         * )
         * @Method({"GET","PUT"})
         * @Template("VitelogeFrontBundle:Ad:redirect_new.html.twig")
         */
        public function viewAction(Request $request,$id, $description) {
            return parent::viewAction($request,$id,$description);

        }


    }


}
