<?php

namespace Viteloge\FrontendBundle\Controller {

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Pagerfanta\Pagerfanta;
    use Pagerfanta\Adapter\DoctrineORMAdapter;
    use Viteloge\InseeBundle\Entity\InseeCity;
    use Viteloge\InseeBundle\Entity\InseeDepartment;
    use Viteloge\CoreBundle\Entity\QueryStats;
    use Viteloge\CoreBundle\SearchEntity\Ad as AdSearch;
    use Viteloge\FrontendBundle\Form\Type\AdSearchType;

    /**
     * @Route("/query")
     */
    class QueryStatsController extends Controller {

        /**
         * Display queries stats for a city
         * Private cache
         *
         * @Route(
         *      "/city/{name}/{id}/{page}/{limit}",
         *      requirements={
         *          "id"="(?:2[a|b|A|B])?0{0,2}\d+",
         *          "page"="\d+",
         *          "limit"="\d+"
         *      },
         *      defaults={
         *          "page" = "1",
         *          "limit" = "100"
         *      },
         *      name="viteloge_frontend_querystats_city",
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
         * @Cache(expires="tomorrow", public=false)
         * @Template()
         */
        public function cityAction(Request $request, InseeCity $inseeCity, $page, $limit) {
            $translated = $this->get('translator');

            // SEO
            $canonicalLink = $this->get('router')->generate($request->get('_route'), $request->get('_route_params'), true);
            $this->container->get('viteloge_frontend_generate.seo')->genereCanonicalSeo('index, follow',$translated->trans('viteloge.frontend.querystats.city.title', array('%city%' => $inseeCity->getName())),$translated->trans('viteloge.frontend.querystats.city.description', array('%city%' => $inseeCity->getName())),$canonicalLink);
            // --

            // Breadcrumbs
            $breadcrumbs = $this->get('viteloge_frontend_generate.breadcrump')->getClassicBreadcrump($inseeCity);
            $breadcrumbs->addItem(
                $translated->trans('breadcrumb.querystats.city', array('%city%' => $inseeCity->getName()), 'breadcrumbs')
            );
            // --

            $em = $this->getDoctrine()->getManager();
            $queryBuilder = $em->createQueryBuilder()
                ->select('qs')
                ->from('VitelogeCoreBundle:QueryStats', 'qs')
                ->where('qs.inseeCity = :inseeCity')
                ->setParameter('inseeCity', $inseeCity)
            ;

            $adapter = new DoctrineORMAdapter($queryBuilder);
            $pagination = new Pagerfanta($adapter);
            $pagination->setCurrentPage($page);
            $pagination->setMaxPerPage($limit);

            return array(
                'inseeCity' => $inseeCity,
                'queries' => $pagination->getCurrentPageResults(),
                'pagination' => $pagination
            );
        }

        /**
         * Legacy function used in order to have some old compatibilities
         * @Route(
         *      "/legacy/{slug}",
         *      defaults={},
         *      name="viteloge_frontend_querystats_legacy"
         * )
         * @Method({"GET"})
         * @ParamConverter(
         *     "queryStats",
         *     class="VitelogeCoreBundle:QueryStats",
         *     options={
         *          "repository_method" = "findOneByUrlrewrite",
         *          "mapping" = {
         *              "slug": "urlrewrite"
         *          },
         *          "map_method_signature": true
         *     }
         * )
         * @Cache(expires="tomorrow", public=true)
         *
         * @deprecated
         */
        public function legacyAction(Request $request, QueryStats $queryStats) {
            return $this->redirectToRoute(
                'viteloge_frontend_querystats_ad',
                array(
                    'slug' => $queryStats->getSlug()
                ),
                301
            );
        }

        /**
         * Ads from a querystats url
         * Cache is set from set last timestamp
         *
         * @Route(
         *      "/ad/{slug}/{page}/{limit}",
         *      defaults={},
         *      requirements={
         *         "page"="\d+",
         *         "limit"="\d+"
         *      },
         *      defaults={
         *         "page"=1,
         *         "limit"="25"
         *      },
         *      name="viteloge_frontend_querystats_ad"
         * )
         * @Method({"GET","PUT"})
         * @ParamConverter(
         *     "queryStats",
         *     class="VitelogeCoreBundle:QueryStats",
         *     options={
         *          "repository_method" = "findOneByUrlrewrite",
         *          "mapping" = {
         *              "slug": "urlrewrite"
         *          },
         *          "map_method_signature": true
         *     }
         * )
         * @Template("VitelogeFrontendBundle:QueryStats:ad.html.twig")
         * @Cache(lastModified="queryStats.getUpdateAt()", ETag="'QueryStats' ~ queryStats.getId() ~ queryStats.getTimestamp()")
         */
        public function adAction(Request $request, QueryStats $queryStats, $page, $limit) {
            $translated = $this->get('translator');

            $em = $this->getDoctrine()->getManager();
            $queryStats->setCount($queryStats->getCount()+1);
            $em->persist($queryStats);
            $em->flush();

            $inseeDepartment = $queryStats->getInseeDepartment();
            $inseeCity = $queryStats->getInseeCity();

            // BUGFIX: querystats id is not null but inseeCity does not exist
            if ($inseeDepartment instanceof InseeDepartment) {
                try {
                    $inseeDepartment->__load();
                } catch (\Doctrine\ORM\EntityNotFoundException $e) {
                    $inseeDepartment = null;
                }
            }
            if ($inseeCity instanceof InseeCity) {
                try {
                    $inseeCity->__load();
                } catch (\Doctrine\ORM\EntityNotFoundException $e) {
                    $inseeCity = null;
                }
            }
            // --

            $adSearch = new AdSearch();
            $adSearch->setTransaction($queryStats->getTransaction());
            $adSearch->setWhat(ucfirst($queryStats->getType()));
            $adSearch->setRooms($queryStats->getRooms());

            if ($inseeCity instanceof InseeCity) {
                $adSearch->setWhere($inseeCity->getId());
                $adSearch->setLocation($inseeCity->getLocation());
            }

            $form = $this->createForm(AdSearchType::class, $adSearch);

            // Save session
            $session = $request->getSession();
            $session->set('adSearch', $adSearch);
            // --

            // Breadcrumbs
            $breadcrumbs = $this->get('viteloge_frontend_generate.breadcrump')->getDeptAndCityBreadcrump($inseeDepartment,$inseeCity);
            $breadcrumbTitle = $queryStats->getKeywords();
            $breadcrumbs->addItem($breadcrumbTitle);

            // elastica
            $elasticaManager = $this->container->get('fos_elastica.manager');
            $repository = $elasticaManager->getRepository('VitelogeCoreBundle:Ad');
            $repository->setEntityManager($this->getDoctrine()->getManager());
            $pagination = $repository->searchPaginated($form->getData());
            // --

            // pager
            $pagination->setMaxPerPage($limit);
            $pagination->setCurrentPage($page);
            // --

            // SEO
            $canonicalLink = $this->get('router')->generate(
                $request->get('_route'),
                $request->get('_route_params'),
                true
            );
            $cityTitle = '';
            if ($inseeCity instanceof InseeCity) {
                $cityTitle = $inseeCity->getFullname().' ('.$inseeCity->getInseeDepartment()->getId().')';
            }
            $this->container->get('viteloge_frontend_generate.seo')->genereCanonicalSeo('index, follow',$breadcrumbTitle.' - '.$translated->trans('viteloge.frontend.querystats.ad.title', array('%city%' => $cityTitle, '%keywords%' => $queryStats->getKeywords())),$breadcrumbTitle.' - '.$translated->trans('viteloge.frontend.querystats.ad.description', array('%city%' => $cityTitle, '%keywords%' => $queryStats->getKeywords())),$canonicalLink);
            // --
              $session->set('totalResult',$pagination->getNbResults());
              $session->set('resultAd',$pagination->getCurrentPageResults());
            return array(
                'form' => $form->createView(),
                'queryStats' => $queryStats,
                'ads' => $pagination->getCurrentPageResults(),
                'pagination' => $pagination,
            );
        }

        /**
         * Display latest query stats
         * Ajax call so we can use public cache
         *
         * @Route(
         *     "/latest/{limit}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit" = "9"
         *     },
         *     name="viteloge_frontend_querystats_latest_limited"
         * )
         * @Route(
         *     "/latest/",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit" = "9"
         *     },
         *     name="viteloge_frontend_querystats_latest"
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:QueryStats:latest.html.twig")
         */
        public function latestAction(Request $request, $limit) {
            $repository = $this->getDoctrine()
                ->getRepository('VitelogeCoreBundle:QueryStats');
            $queries = $repository->findByFiltered(
                $request->query->all(),
                array( 'timestamp' => 'DESC' ),
                $limit
            );
            return array(
                'queries' => $queries
            );
        }

    }

}
