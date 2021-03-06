<?php

namespace Viteloge\FrontendBundle\Controller {

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Viteloge\InseeBundle\Entity\InseeCity;
    use Viteloge\CoreBundle\Entity\Api;
    use Viteloge\FrontendBundle\Form\Type\ApiType;
    use Viteloge\FrontendBundle\Form\Type\AdSearchType;
    use Viteloge\CoreBundle\SearchEntity\Ad as AdSearch;

    /**
     * Api controller.
     *
     * @Route("/api")
     */
    class ApiController extends Controller {

        /**
         * Creates a form to create a Api entity.
         *
         * @param Api $api The entity
         * @return \Symfony\Component\Form\Form The form
         */
        private function createCreateForm(Api $api) {
            return $this->createForm(
                ApiType::class,
                $api,
                array(
                    'action' => $this->generateUrl('viteloge_frontend_api_create'),
                    'method' => 'POST'
                )
            );
        }

        /**
         * Displays a form to create a new Api entity.
         * Private cache
         *
         * @Route(
         *      "/new",
         *      name="viteloge_frontend_api_new"
         * )
         * @Cache(expires="tomorrow", public=false)
         * @Method("GET")
         * @Template("VitelogeFrontendBundle:Api:new.html.twig")
         */
        public function newAction(Request $request) {
            $api = new Api();
            $form = $this->createCreateForm($api);

            return array(
                'api' => $api,
                'form' => $form->createView(),
            );
        }

        /**
         * Creates a new Api entity.
         * No cache for a post
         *
         * @Route(
         *      "/",
         *      name="viteloge_frontend_api_create"
         * )
         * @Method("POST")
         * @Template("VitelogeFrontendBundle:Api:new.html.twig")
         */
        public function createAction(Request $request) {
            $api = new Api();
            $form = $this->createCreateForm($api);
            $form->handleRequest($request);

            if ($form->isValid()) {
                // just display the form
            }

            return array(
                'api' => $api,
                'form' => $form->createView(),
            );
        }

        /**
         * Show included form research
         * Private cache
         *
         * @Route(
         *      "/show/{id}",
         *      requirements={
         *         "id"="(?:2[a|b|A|B])?0{0,2}\d+"
         *      },
         *      name="viteloge_frontend_api_show"
         * )
         * @Cache(expires="tomorrow", public=false)
         * @Method("GET")
         * @ParamConverter("inseeCity", class="VitelogeInseeBundle:InseeCity", options={"id" = "id"})
         * @Template("VitelogeFrontendBundle:Api:show.html.twig")
         */
        public function showAction(Request $request, InseeCity $inseeCity) {
            $includeLibs = true;
            if ($request->query->has('includeLibs')) {
                $includeLibs = (bool)$request->query->get('includeLibs');
            }
            $adSearch = new AdSearch();
            $adSearch->setWhere(array($inseeCity));
            $form = $this->createForm(AdSearchType::class, $adSearch);
            return array(
                'inseeCity' => $inseeCity,
                'form' => $form->createView(),
                'includeLibs' => $includeLibs
            );
        }

        /**
         * Show included form research like the legacy api
         *
         * @Route(
         *      "/legacy/{id}",
         *      requirements={
         *         "id"="(?:2[a|b|A|B])?0{0,2}\d+"
         *      },
         *      name="viteloge_frontend_api_legacy"
         * )
         * @Method("GET")
         * @ParamConverter("inseeCity", class="VitelogeInseeBundle:InseeCity", options={"id" = "id"})
         * @Template("VitelogeFrontendBundle:Api:legacy.html.twig")
         */
        public function legacyAction(Request $request, InseeCity $inseeCity) {
            $adSearch = new AdSearch();
            $adSearch->setWhere(array($inseeCity));
            $form = $this->createForm(AdSearchType::class, $adSearch);
            return array(
                'inseeCity' => $inseeCity,
                'form' => $form->createView()
            );
        }

    }


}
