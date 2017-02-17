<?php

namespace Viteloge\FrontendBundle\Controller {

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Viteloge\InseeBundle\Entity\InseeState;
    use Viteloge\InseeBundle\Entity\InseeDepartment;
    use Viteloge\InseeBundle\Entity\InseeCity;
    use Viteloge\InseeBundle\Entity\InseeArea;
    use Viteloge\CoreBundle\SearchEntity\Ad as AdSearch;
    use Viteloge\FrontendBundle\Form\Type\AdSearchType;

    /**
     * @Route("/glossary")
     */
    class GlossaryController extends Controller {


        /**
         * Legacy function used because there is no slug saved in table
         * There is no cache
         *
         * @Route(
         *      "/{slug}",
         *      defaults={},
         *      name="viteloge_frontend_glossary_legacy"
         * )
         * @Method({"GET"})
         * @Template()
         */
        public function legacyAction(Request $request, $slug) {
            $queries = array_merge(
                $request->query->all(),
                $request->request->all()
            );

            if ($slug == 'france') {
                return $this->redirectToRoute(
                    'viteloge_frontend_ad_search',
                    $queries,
                    301
                );
            }

            $repository = $this->getDoctrine()
                ->getRepository('VitelogeInseeBundle:InseeState');
            $inseeEntity = $repository->findOneBySoundex($slug);
            if ($inseeEntity instanceof InseeState) {
                $options = array_merge(
                    array(
                        'name' => $inseeEntity->getSlug(),
                        'id' => $inseeEntity->getId()
                    ),
                    $queries
                );
                return $this->redirectToRoute(
                    'viteloge_frontend_glossary_showstate',
                    $options,
                    301
                );
            }

            $repository = $this->getDoctrine()
                ->getRepository('VitelogeInseeBundle:InseeDepartment');
            $inseeEntity = $repository->findOneBySoundex($slug);
            if ($inseeEntity instanceof InseeDepartment) {
                $options = array_merge(
                    array(
                        'name' => $inseeEntity->getSlug(),
                        'id' => $inseeEntity->getId()
                    ),
                    $queries
                );
                return $this->redirectToRoute(
                    'viteloge_frontend_glossary_showdepartment',
                    $options,
                    301
                );
            }

            throw $this->createNotFoundException();
        }

        /**
         * Display the most searched cities
         * Ajax call so we could have a shared public cache
         *
         * @Route(
         *     "/mostSearched/{limit}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit"="5"
         *     },
         *     name="viteloge_frontend_glossary_mostsearched_limited"
         * )
         * @Route(
         *     "/mostSearched/",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit" = "5"
         *     },
         *     name="viteloge_frontend_glossary_mostsearched"
         * )
         * @Cache(smaxage="604800", maxage="604800", public=true)
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Glossary:mostSearched.html.twig")
         */
        public function mostSearchedAction(Request $request, $limit=5, array $criteria=array()) {
            $repository = $this->getDoctrine()
                ->getRepository('VitelogeCoreBundle:UserSearch');
            $glossary = $repository->findAllInseeCityOrderedByCount();
            return $this->render(
                'VitelogeFrontendBundle:Glossary:mostSearched.html.twig',
                array(
                    'glossary' => $glossary,

                )
            );
        }

        /**
         * Display the most searched cities
         * Ajax call so we could have a shared public cache
         *
         * @Route(
         *     "/mostSearchedTransaction/{transaction}/{type}/{limit}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit"="5"
         *     },
         *     name="viteloge_frontend_glossary_mostsearched_tansaction_limited"
         * )
         * @Route(
         *     "/mostSearchedTransaction/{transaction}/{type}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit" = "5"
         *     },
         *     name="viteloge_frontend_glossary_tansaction_mostsearched"
         * )
         * @Cache(smaxage="604800", maxage="604800", public=true)
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Glossary:mostSearchedTransaction.html.twig")
         */
        public function mostSearchedTransactionAction(Request $request,$transaction,$type, $limit=5) {
            $repository = $this->getDoctrine()
                ->getRepository('VitelogeCoreBundle:UserSearch');
            $glossary = $repository->findAllInseeCityTransactionOrderedByCount($transaction, $type, $limit);
            return array(
                    'glossary' => $glossary,
                    'transaction' => $transaction,
                    'type' => $type,);

        }


        /**
         * Display the most searched cities
         * Ajax call so we could have a shared public cache
         *
         * @Route(
         *     "/mostSearchedTownTransaction/{id}/{radius}/{transaction}/{type}/{limit}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit"="5"
         *     },
         *     name="viteloge_frontend_glossary_town_transaction_limited"
         * )
         * @Route(
         *     "/mostSearchedTownTransaction/{id}/{radius}/{transaction}/{type}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit" = "5"
         *     },
         *     name="viteloge_frontend_glossary_town_transaction"
         * )
         *
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Glossary:mostSearchedTownTransaction.html.twig")
         */
        public function mostSearchedTownTransactionAction(Request $request,InseeCity $inseeCity, $radius,$transaction,$type, $limit=5) {
            $finder = $this->container->get('fos_elastica.finder.viteloge.inseeCity');
            $radiusDistanceQuery = new \Elastica\Filter\GeoDistance('location', $inseeCity->getLocation(), $radius.'km');
            $cities = $finder->find($radiusDistanceQuery, $limit);
            return $this->render(
                'VitelogeFrontendBundle:Glossary:mostSearchedTownTransaction.html.twig',
                array(
                    'city' => $inseeCity,
                    'cities' => $cities,
                    'transaction' => $transaction,
                    'type' => $type,
                )
            );
        }


        /**
         * Research from state
         * Expire tomorrow
         *
         * @Route(
         *     "/state/{name}/{id}",
         *     requirements={
         *         "id"="\d+"
         *     },
         *     name="viteloge_frontend_glossary_showstate"
         * )
         * @Method({"GET", "POST"})
         * @ParamConverter(
         *     "inseeState",
         *     class="VitelogeInseeBundle:InseeState",
         *     options={
         *         "id" = "id",
         *         "name" = "name",
         *         "exclude": {
         *             "name"
         *         }
         *     }
         * )
         * @Cache(expires="tomorrow", public=true)
         */
        public function showStateAction(Request $request, InseeState $inseeState) {

            $queries = array_merge(
                $request->query->all(),
                $request->request->all()
            );
            $options = array_merge(
                array(
                    'whereState' => array( $inseeState->getId() )
                ),
                $queries
            );

            return $this->redirectToRoute('viteloge_frontend_ad_search', $options, 301);
        }

        /**
         * Search from department
         * Expire tomorrow
         *
         * @Route(
         *     "/department/{name}/{id}",
         *     requirements={
         *         "id"="(?:2[a|b|A|B])|\d+"
         *     },
         *     name="viteloge_frontend_glossary_showdepartment"
         * )
         * @Method({"GET", "POST"})
         * @ParamConverter(
         *     "inseeDepartment",
         *     class="VitelogeInseeBundle:InseeDepartment",
         *     options={
         *         "id" = "id",
         *         "name" = "name",
         *         "exclude": {
         *             "name"
         *         }
         *     }
         * )
         * @Cache(expires="tomorrow", public=true)
         */
        public function showDepartmentAction(Request $request, InseeDepartment $inseeDepartment) {

            $queries = array_merge(
                $request->query->all(),
                $request->request->all()
            );
            $options = array_merge(
                array(
                    'whereDepartment' => array( $inseeDepartment->getId() )
                ),
                $queries
            );

            return $this->redirectToRoute('viteloge_frontend_ad_search', $options, 301);
        }

        /**
         * Display city page information
         * Private cache
         *
         * @Route(
         *     "/city/{name}/{id}",
         *     requirements={
         *         "id"="(?:2[a|b|A|B])?0{0,2}\d+"
         *     },
         *     name="viteloge_frontend_glossary_showcity"
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
         * @Template("VitelogeFrontendBundle:Glossary:showCity.html.twig")
         */
        public function showCityAction(Request $request, InseeCity $inseeCity) {
            $translated = $this->get('translator');

            // Load city data
            $repository = $this->getDoctrine()
                ->getRepository('VitelogeCoreBundle:CityData');
            $cityData = $repository->findOneByInseeCity($inseeCity);
            // --

            //
            $repository = $this->getDoctrine()
                ->getRepository('VitelogeCoreBundle:Ad');
            $count = $repository->countByFiltered(array('inseeCity' => $inseeCity));
            // --

            // Breadcrumbs
            $breadcrumbs = $this->get('white_october_breadcrumbs');
            $breadcrumbs->addItem(
                'Home', $this->get('router')->generate('viteloge_frontend_homepage')
            );
            if ($inseeCity->getInseeState()) {
                $breadcrumbs->addItem(
                    $inseeCity->getInseeState()->getFullname(),
                    $this->get('router')->generate('viteloge_frontend_glossary_showstate',
                        array(
                            'name' => $inseeCity->getInseeState()->getSlug(),
                            'id' => $inseeCity->getInseeState()->getId()
                        )
                    )
                );
            }
            if ($inseeCity->getInseeDepartment()) {
                $breadcrumbs->addItem(
                    $inseeCity->getInseeDepartment()->getFullname(),
                    $this->get('router')->generate('viteloge_frontend_glossary_showdepartment',
                        array(
                            'name' => $inseeCity->getInseeDepartment()->getSlug(),
                            'id' => $inseeCity->getInseeDepartment()->getId()
                        )
                    )
                );
            }
            $breadcrumbs->addItem('Immobilier '.$inseeCity->getFullname());
            // --

            // Google map api
            $mapOptions = new \StdClass();
            $mapOptions->zoom = 12;
            $mapOptions->lat = $inseeCity->getLat();
            $mapOptions->lng = $inseeCity->getLng();
            $mapOptions->disableDefaultUI = true;
            $mapOptions->scrollwheel = false;
            // --

            // SEO
            $canonicalLink = $this->get('router')->generate(
                $request->get('_route'),
                array(
                    'name' => $inseeCity->getSlug(),
                    'id' => $inseeCity->getId()
                ),
                true
            );
            $cityTitle = $inseeCity->getFullname().' ('.$inseeCity->getInseeDepartment()->getId().')';
            $seoPage = $this->container->get('sonata.seo.page');
            $seoPage
                ->setTitle($translated->trans('viteloge.frontend.glossary.showcity.title.city', array('%city%' => $cityTitle)))
                ->addMeta('name', 'description', $translated->trans('viteloge.frontend.glossary.showcity.description.city', array('%city%' => $cityTitle)))
                ->addMeta('property', 'og:title', $translated->trans('viteloge.frontend.glossary.showcity.title.city', array('%city%' => $cityTitle)))
                ->addMeta('property', 'og:type', 'website')
                ->addMeta('property', 'og:description', $translated->trans('viteloge.frontend.glossary.showcity.description.city', array('%city%' => $cityTitle)))
                ->addMeta('property', 'og:url',  $canonicalLink)
                ->addMeta('property', 'geo.region', 'FR')
                ->addMeta('property', 'geo.placename', $inseeCity->getFullname())
                ->addMeta('property', 'geo.position', $inseeCity->getLat().';'.$inseeCity->getLng())
                ->addMeta('property', 'ICMB', $inseeCity->getLat().','.$inseeCity->getLng())
                ->setLinkCanonical($canonicalLink)
            ;
            // --
             // Form
            $session = $request->getSession();
            $requestSearch = $session->get('request');
            $adSearch = new AdSearch();
            $adSearch->handleRequest($requestSearch);
            $form = $this->createForm(AdSearchType::class, $adSearch);

            return array(
                'city' => $inseeCity,
                'cityData' => $cityData,
                'count' => $count,
                'mapOptions' => $mapOptions,
                'form' => $form->createView(),

            );
        }

        /**
         * Search from area
         * Expire tomorrow
         *
         * @Route(
         *     "/area/{name}/{id}",
         *     requirements={
         *         "id"="\d+"
         *     },
         *     name="viteloge_frontend_glossary_showarea"
         * )
         * @Method({"GET", "POST"})
         * @ParamConverter(
         *     "inseeArea",
         *     class="VitelogeInseeBundle:InseeArea",
         *     options={
         *         "id" = "id",
         *         "name" = "name",
         *         "exclude": {
         *             "name"
         *         }
         *     }
         * )
         * @Cache(expires="tomorrow", public=true)
         */
        public function showAreaAction(Request $request, InseeArea $inseeArea) {
            $options = array(
                'whereArea' => array( $inseeArea->getId() )
            );
            return $this->redirectToRoute('viteloge_frontend_ad_search', $options, 301);
        }

        /**
         *
         *
         * @Route(
         *     "/cities/{id}/",
         *     requirements={
         *         "id"="\d+"
         *     },
         *     name="viteloge_frontend_glossary_city"
         * )
         * @Method({"GET"})
         * @ParamConverter("inseeDepartment", class="VitelogeInseeBundle:InseeDepartment", options={"id" = "id"})
         * @Cache(expires="tomorrow", public=true)
         * @Template("VitelogeFrontendBundle:Glossary:cities.html.twig")
         */
        public function citiesAction(Request $request, InseeDepartment $inseeDepartment) {
            $repository = $this->getDoctrine()
                ->getRepository('VitelogeInseeBundle:InseeCity');
            $cities = $repository->findByInseeDepartment($inseeDepartment);
            return array(
                'cities' => $cities
            );
        }

        /**
         *
         *
         * @Route(
         *      "/areas/{id}/",
         *      requirements={
         *          "id"="(?:2[a|b|A|B])?0{0,2}\d+"
         *      },
         *      name="viteloge_frontend_glossary_areas"
         * )
         * @Method({"GET"})
         * @ParamConverter("inseeCity", class="VitelogeInseeBundle:InseeCity", options={"id" = "id"})
         * @Cache(expires="tomorrow", public=true)
         * @Template("VitelogeFrontendBundle:Glossary:areas.html.twig")
         */
        public function areasAction(Request $request, inseeCity $inseeCity) {
            $repository = $this->getDoctrine()
                ->getRepository('VitelogeInseeBundle:InseeArea');
            $areas = $repository->findByInseeCity($inseeCity);
            return array(
                'areas' => $areas
            );
        }

    }

}
