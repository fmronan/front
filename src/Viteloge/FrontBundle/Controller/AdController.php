<?php

namespace Viteloge\FrontBundle\Controller {

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Serializer\Serializer;
    use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
    use Symfony\Component\Serializer\Encoder\JsonEncoder;
    use Viteloge\InseeBundle\Entity\InseeCity;
    use Viteloge\InseeBundle\Entity\InseeState;
    use Viteloge\InseeBundle\Entity\InseeDepartment;
    use Viteloge\CoreBundle\Entity\Ad;
    use Viteloge\CoreBundle\Entity\QueryStats;
    use Viteloge\CoreBundle\Entity\WebSearch;
    use Viteloge\CoreBundle\Entity\UserSearch;
    use Viteloge\CoreBundle\Component\Enum\DistanceEnum;
    use Viteloge\CoreBundle\SearchEntity\Ad as AdSearch;
    use Viteloge\FrontendBundle\Form\Type\AdSearchType;
    use Viteloge\FrontendBundle\Controller\AdController as BaseController;


    /**
     * Note: This should be the search ad controller
     * @Route("/ad")
     */
    class AdController extends BaseController {


        /**
         * Display research result. No cache for this page
         *
         * @Route(
         *     "/search/{page}/{limit}",
         *     requirements={
         *         "page"="\d+",
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "page"=1,
         *         "limit"="25"
         *     },
         *     name="viteloge_frontend_ad_search"
         * )
         * @Route(
         *     "/search/",
         *     requirements={
         *         "page"="\d+",
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "page"=1,
         *         "limit"="25"
         *     },
         *     name="viteloge_frontend_ad_search_default"
         * )
         * @Method({"GET","PUT"})
         * @Template("VitelogeFrontBundle:Ad:search_response.html.twig")
         */
        public function searchAction(Request $request, $page, $limit) {
           return parent::searchAction( $request, $page, $limit);
        }

        /**
         * Search form.
         * No cache
         *
         * @Route(
         *     "/search/from/form/",
         *     name="viteloge_frontend_ad_searchfromform"
         * )
         * @Method({"POST"})
         * @Template("VitelogeFrontendBundle:Ad:search_from_form.html.twig")
         */
        public function searchFromFormAction(Request $request) {
            return parent::searchFromFormAction( $request);
        }


    }


}
