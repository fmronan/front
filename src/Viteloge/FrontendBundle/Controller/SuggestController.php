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

    /**
     * @Route("/suggest")
     */
    class SuggestController extends Controller {

        /**
         * Display cities suggestion for a query
         * Public cache
         *
         * @Route(
         *     "/cities/{_format}",
         *      requirements={
         *          "_format"="html|json"
         *      },
         *      defaults={
         *          "_format"="json"
         *      },
         *      name="viteloge_frontend_suggest_cities",
         *      options = {
         *          "i18n" = true
         *      }
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Template()
         */
        public function citiesAction( Request $request, $_format ) {
            $options = array(
                'sort' => array(
                    'isCapital' => array( 'order' => 'desc' ),
                    'population' => array( 'order' => 'desc' )
                )
            );

            $search = $request->get('q', '');
            $search = \Elastica\Util::escapeTerm($search);
            $index = $this->container->get('fos_elastica.finder.viteloge.inseeCity');
            $searchQuery = new \Elastica\Query\QueryString();
            $searchQuery->setParam('query', $search);
            $searchQuery->setDefaultOperator('AND');
            $searchQuery->setParam('fields', array(
                'name',
                'postalCode',
            ));

            $cities = $index->find($searchQuery, $options);


            return array(
                'cities' => $cities
            );
        }

        /**
         * Display a list of cities near to another
         * Ajax so we could have public cache
         *
         * @Route(
         *      "/near/{name}/{id}/{radius}/{limit}/{_format}",
         *      requirements={
         *          "id"="(?:2[a|b|A|B])?0{0,2}\d+",
         *          "radius"="\d+",
         *          "limit"="\d+",
         *          "_format"="html|json"
         *      },
         *      defaults={
         *          "radius" = "10",
         *          "limit" = "10",
         *          "_format"="json"
         *      },
         *      name="viteloge_frontend_suggest_near",
         *      options = {
         *         "i18n" = true
         *      }
         * )
         * @Method({"GET", "POST"})
         * @ParamConverter(
         *     "inseeCity",
         *     class="VitelogeInseeBundle:InseeCity",
         *     options={
         *         "id" = "id",
         *         "name" = "name",
         *         "exclude": {
         *             "name"
         *         }
         *     }
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Template()
         */
        public function nearAction( Request $request, InseeCity $inseeCity, $radius, $limit, $_format ) {
            $finder = $this->container->get('fos_elastica.finder.viteloge.inseeCity');
            $radiusDistanceQuery = new \Elastica\Filter\GeoDistance('location', $inseeCity->getLocation(), $radius.'km');
            $cities = $finder->find($radiusDistanceQuery, $limit);
            return array(
                'city' => $inseeCity,
                'cities' => $cities
            );
        }

        /**
         * Display states suggestion for a query
         * Public cache
         *
         * @Route(
         *     "/states/{_format}",
         *      requirements={
         *          "_format"="html|json"
         *      },
         *      defaults={
         *          "_format"="json"
         *      },
         *     name="viteloge_frontend_suggest_states",
         *     options = {
         *         "i18n" = true
         *     }
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Template()
         */
        public function statesAction( Request $request, $_format ) {
            $search = $request->get('q', '');
            $search = \Elastica\Util::escapeTerm($search);

            $repository = $this->getDoctrine()
                ->getRepository('VitelogeInseeBundle:InseeState');
            $result = $repository->findAll();

            return array(
                'states' => $result
            );
        }

        /**
         * Display departments suggestion for a query
         * Public cache
         *
         * @Route(
         *     "/departments/{_format}",
         *      requirements={
         *          "_format"="html|json"
         *      },
         *      defaults={
         *          "_format"="json"
         *      },
         *     name="viteloge_frontend_suggest_departments",
         *     options = {
         *         "i18n" = true
         *     }
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Template()
         */
        public function departmentsAction( Request $request, $_format) {
            $search = $request->get('q', '');
            $search = \Elastica\Util::escapeTerm($search);

            $repository = $this->getDoctrine()
                ->getRepository('VitelogeInseeBundle:InseeDepartment');
            $result = $repository->findAll();

            return array(
                'departments' => $result
            );
        }

    }

}
