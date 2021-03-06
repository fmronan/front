<?php

namespace Viteloge\FrontendBundle\Controller {

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

    /**
     * Note: This should be the search ad controller
     * @Route("/ad")
     */
    class AdController extends Controller {

        /**
         * Return the number of ads in database.
         * Usefull for pro and part website
         *
         * @Route(
         *      "/count",
         *      defaults={
         *          "_format"="txt"
         *      },
         *      name="viteloge_frontend_ad_count_format",
         * )
         * @Route(
         *      "/count.{_format}",
         *      requirements={
         *          "_format"="txt"
         *      },
         *      defaults={
         *          "_format"="txt"
         *      },
         *      name="viteloge_frontend_ad_count_format",
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Template()
         */
        public function countAction(Request $request, $_format) {
            // This count is pretty faster than an elastic search count
            $repository = $this->getDoctrine()
                ->getRepository('VitelogeCoreBundle:Ad');
            $count = $repository->countByFiltered();
            return array(
                'count' => $count
            );
        }

        private function getBreadcrumpAction(Request $request,$adSearch,$translated){
            $lieu='';
             // First State
            $inseeState = null;
            $whereState = $adSearch->getWhereState();
            if (!empty($whereState)) {
                $stateId = current($whereState);
                $stateRepository = $this->getDoctrine()->getRepository('VitelogeInseeBundle:InseeState');
                $inseeState = $stateRepository->find((int)$stateId);
                $lieu = 'en '.$inseeState->getFullname();
            }
            // --

            // First Department
            $inseeDepartment = null;
            $whereDepartment = $adSearch->getWhereDepartment();
            if (!empty($whereDepartment)) {
                $departmentId = current($whereDepartment);
                $departmentRepository = $this->getDoctrine()->getRepository('VitelogeInseeBundle:InseeDepartment');
                $inseeDepartment = $departmentRepository->find((int)$departmentId);
                $lieu = 'en '.$inseeDepartment->getFullname();
            }

            // --

            // First city
            $inseeCity = null;
            $where = $adSearch->getWhere();
            if (!empty($where)) {
                $cityId = current($where);
                $cityRepository = $this->getDoctrine()->getRepository('VitelogeInseeBundle:InseeCity');
                $inseeCity = $cityRepository->find((int)$cityId);
            }
            // --

            // Improve search for specifics city
            if ($inseeCity instanceof InseeCity) {
                $radius = $adSearch->getRadius();
                $adSearch->setLocation($inseeCity->getLocation());
                $lieu = 'à '.$inseeCity->getFullname();
                if ($inseeCity->getGeolevel() == 'ARM' && empty($radius)) {
                    $adSearch->setRadius(DistanceEnum::FIVE);
                }
            }
            // --

            // Breadcrumbs
            $transaction = $adSearch->getTransaction();

            $description = 'Toutes les annonces immobilières de ';
            $breadcrumbs = $this->get('viteloge_frontend_generate.breadcrump')->getDeptAndCityBreadcrump($inseeDepartment,$inseeCity,$transaction);
            if ($inseeState instanceof InseeState) {
                $breadcrumbTitle  = (!empty($transaction)) ? $translated->trans('ad.transaction.'.strtoupper($transaction)).' ' : '';
                $breadcrumbTitle .= $inseeState->getFullname();
                $breadcrumbs->addItem(
                    $breadcrumbTitle,
                    $this->get('router')->generate('viteloge_frontend_ad_search',
                        array(
                            'transaction' => $transaction,
                            'whereState' => array($inseeState->getId())
                        )
                    )
                );
            }

            // No QueryStats SEO Optimization
            $qsId = $request->get('qs');
            if (empty($qsId)) {
                $what = $adSearch->getWhat();
                $breadcrumbTitleSuffix = '';
                $breadcrumbTitleSuffix .= (!empty($what)) ? implode(', ', $what).' ' : ' ';
                $suffix = '';
                $suffix .= (!empty($what)) ? implode(' , ', $what).'s' : ' biens immobilier ';
                $title = '';
                $titre ='';
                if($transaction[0] == 'V'){
                   $title .= ' ventes ';
                   $titre .= ' à vendre ';
               }elseif($transaction[0] == 'L'){
                   $title .= ' locations ';
                   $titre .= ' à louer ';
               }elseif($transaction[0] == 'N'){
                   $title .= ' programmes neufs ';
                   $titre .= ' neufs ';
               }else{
                   $titre .=' à vendre et à louer';
               }

                $description .= $title.$suffix;
                $description .= ($inseeCity instanceof InseeCity) ? $inseeCity->getFullname() : '';
                $description .= ($inseeDepartment instanceof InseeDepartment) ? $inseeDepartment->getFullname() : '';
                $description .= ($inseeState instanceof InseeState) ? $inseeState->getFullname() : '';
                $description .= '. Retrouvez';
                if($suffix == 'Maison'){
                    $description .= ' toutes nos '.$suffix;
                }else{
                     $description .= ' tous nos '.$suffix;
                }


                $description .= $titre.' a ';
                $description .= ($inseeCity instanceof InseeCity) ? $inseeCity->getFullname() : '';
                $description .= ($inseeDepartment instanceof InseeDepartment) ? $inseeDepartment->getFullname() : '';
                $description .= ($inseeState instanceof InseeState) ? $inseeState->getFullname() : '';


                $breadcrumbTitleSuffix .= ($inseeCity instanceof InseeCity) ? $inseeCity->getFullname() : '';
                $breadcrumbTitleSuffix .= ($inseeDepartment instanceof InseeDepartment) ? $inseeDepartment->getFullname() : '';
                $breadcrumbTitleSuffix .= ($inseeState instanceof InseeState) ? $inseeState->getFullname() : '';
                $breadcrumbTitle  = (!empty($transaction)) ? $translated->trans('ad.transaction.'.strtoupper($transaction[0])).' ' : $translated->trans('ad.research').': ';
                $breadcrumbTitle .= (!empty(trim($breadcrumbTitleSuffix))) ? $breadcrumbTitleSuffix : $translated->trans('viteloge.result');

                $breadcrumbs->addItem($breadcrumbTitle);
            }
            // --
            // QueryStats SEO optimisation
            if (!empty($qsId)) {
                $qsRepository = $this->getDoctrine()->getRepository('VitelogeCoreBundle:QueryStats');
                $qs = $qsRepository->find((int)$qsId);
                $breadcrumbTitle = $qs->getKeywords();
                $description .= $breadcrumbTitle;
                $breadcrumbs->addItem($breadcrumbTitle);
            }

             $infos['description']= $description;
             $infos['lieu'] = $lieu;
             $infos['transac'] = $titre;
             $infos['breadcrumbTitle']= $breadcrumbTitle;
             return $infos;
        }

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
         * @Template("VitelogeFrontendBundle:Ad:search_response.html.twig")
         */
        public function searchAction(Request $request, $page, $limit) {
           $translated = $this->get('translator');
           $currentUrl = $request->getUri();


            // Form
            $adSearch = new AdSearch();
            $adSearch->handleRequest($request);
            $form = $this->createForm(AdSearchType::class, $adSearch);
            // --

            // Save session
            $session = $request->getSession();
            $session->set('adSearch', $adSearch);

            $session->set('currentUrl', $currentUrl);
            $session->remove('request');
            $session->set('request', $request);

            // --

           $infos = $this->getBreadcrumpAction($request,$adSearch,$translated);
           $description = $infos['description'];
           $breadcrumbTitle = $infos['breadcrumbTitle'];
            // elastica
            $elasticaManager = $this->container->get('fos_elastica.manager');
            $repository = $elasticaManager->getRepository('VitelogeCoreBundle:Ad');
            $pagination = $repository->searchPaginated($form->getData());
            // --
            // pager
            $pagination->setMaxPerPage($limit);
            $pagination->setCurrentPage($page);
            $session->set('currentPage',$pagination->getCurrentPage());
            // --

            // SEO
            $canonicalLink = $this->get('router')->generate(
                $request->get('_route'),
                $request->get('_route_params')
            );
            $this->container->get('viteloge_frontend_generate.seo')->genereCanonicalSeo('noindex, follow',$breadcrumbTitle.' - '.$translated->trans('viteloge.frontend.ad.search.title'),$description,$canonicalLink,$breadcrumbTitle.' - '.$translated->trans('viteloge.frontend.ad.search.description'));

            if($pagination->hasNextPage() || $pagination->hasPreviousPage()){
              $url = $this->generateUrl('viteloge_frontend_ad_search', array());
            }
            if($pagination->hasNextPage()){
              $next = explode('?', $currentUrl);
              $nextpage = $page+1;
              if(isset($next[1])){
                $nextUrl = $url.'/'.$nextpage.'?'.$next[1];
                $session->set('nextUrl', $nextUrl);
              }else{
                $session->remove('nextUrl');
              }


            }else{
               $session->remove('nextUrl');
            }

            if($pagination->hasPreviousPage()){
              $preview = explode('?', $currentUrl);
              $previewpage = $page-1;
              if(isset($preview[1])){
                $previewUrl = $url.'/'.$previewpage.'?'.$preview[1];
                $session->set('previewUrl', $previewUrl);
            }else{
                $session->remove('previewUrl');
            }


            }else{
               $session->remove('previewUrl');
            }

            // --
            $session->set('totalResult',$pagination->getNbResults());
            $session->remove('totalResultVente');
            $session->set('resultAd',$pagination->getCurrentPageResults());
            return array(
                'form' => $form->createView(),
                'ads' => $pagination->getCurrentPageResults(),
                'pagination' => $pagination,
                'infos'=> $infos,
            );
        }

        /**
         * Legacy, search others ads from a particular ad
         * There are no header information so we could set a good cache
         *
         * @Route(
         *      "/search/from/ad/{id}",
         *      requirements={
         *          "id"="\d+",
         *      },
         *      name="viteloge_frontend_ad_count",
         * )
         * @Cache(lastModified="ad.getUpdatedAt()", ETag="'Ad' ~ ad.getId() ~ ad.getUpdatedAt().getTimestamp()")
         * @Method({"GET"})
         * @ParamConverter("ad", class="VitelogeCoreBundle:Ad", options={"id" = "id"})
         * @Template()
         */
        public function searchFromAdAction(Request $request, Ad $ad) {
            $adSearch = new AdSearch();
            $adSearch->setTransaction($ad->getTransaction());
            $adSearch->setWhat($ad->getType());
            $adSearch->setRooms($ad->getRooms());
            if ($ad->getInseeCity() instanceof InseeCity) {
                $adSearch->setWhere($ad->getInseeCity()->getId());
                $adSearch->setLocation($ad->getInseeCity()->getLocation());
            }

            // transform object to array in order to through it to url
            $encoders = array(new JsonEncoder());
            $normalizers = array(new GetSetMethodNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $options = json_decode($serializer->serialize($adSearch, 'json'), true);

            return $this->redirectToRoute(
                'viteloge_frontend_ad_search',
                $options,
                301
            );
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
            $adSearch = new AdSearch();
            $form = $this->createForm(AdSearchType::class, $adSearch);
            $form->handleRequest($request);
            if ($form->isValid()) {
                // transform object to array in order to through it to url
                $encoders = array(new JsonEncoder());
                $normalizers = array(new GetSetMethodNormalizer());
                $serializer = new Serializer($normalizers, $encoders);
                $options = json_decode($serializer->serialize($form->getData(), 'json'), true);

                if ($request->isXmlHttpRequest()) {
                    $response = new JsonResponse();
                    return $response->setData(array(
                        'redirect' => $this->generateUrl('viteloge_frontend_ad_search', $options)
                    ));
                }

                return $this->redirectToRoute(
                    'viteloge_frontend_ad_search',
                    $options,
                    301
                );
            }

            $options = array(
                'adSearch' => $adSearch,
                'form' => $form->createView()
            );
            if ($request->query->has('hideTransaction')) {
                $options['isTransactionLabelHidden'] = true;
            }
            return $options;
        }

        /**
         * Search from a WebSearch
         * Cache is set from the created date.
         *
         * @Route(
         *     "/search/from/websearch/{id}",
         *     requirements={
         *         "id"="\d+"
         *     },
         *     name="viteloge_frontend_ad_searchfromwebsearch"
         * )
         * @Cache(lastModified="webSearch.getUpdatedAt()", ETag="'WebSearch' ~ webSearch.getId() ~ webSearch.getUpdatedAt().getTimestamp()")
         * @ParamConverter("webSearch", class="VitelogeCoreBundle:WebSearch", options={"id" = "id"})
         * @Method({"GET"})
         */
        public function searchFromWebSearchAction(Request $request, WebSearch $webSearch) {
            $userSearch = $webSearch->getUserSearch();
            return $this->searchFromUserSearchAction($request, $userSearch);
        }

        /**
         * Search from a UserSearch
         * Cache is set from the created date.
         *
         * @Route(
         *     "/search/from/usersearch/{id}",
         *     requirements={
         *         "id"="\d+"
         *     },
         *     name="viteloge_frontend_ad_searchfromusersearch"
         * )
         * @Cache(lastModified="userSearch.getCreatedAt()", ETag="'UserSearch' ~ userSearch.getId() ~ userSearch.getCreatedAt().getTimestamp()")
         * @ParamConverter("userSearch", class="VitelogeCoreBundle:UserSearch", options={"id" = "id"})
         * @Method({"GET"})
         */
        public function searchFromUserSearchAction(Request $request, UserSearch $userSearch) {
            $adSearch = new AdSearch();
            $adSearch->setTransaction($userSearch->getTransaction());
            $adSearch->setWhat($userSearch->getType());
            $adSearch->setRooms($userSearch->getRooms());
            $adSearch->setMinPrice($userSearch->getBudgetMin());
            $adSearch->setMaxPrice($userSearch->getBudgetMax());
            $adSearch->setRadius($userSearch->getRadius());
            $adSearch->setKeywords($userSearch->getKeywords());
            if ($userSearch->getInseeCity() instanceof InseeCity) {
                $adSearch->setWhere($userSearch->getInseeCity()->getId());
                $adSearch->setLocation($userSearch->getInseeCity()->getLocation());
            }
            $adSearch->setSort('createdAt');

            // transform object to array in order to through it to url
            $encoders = array(new JsonEncoder());
            $normalizers = array(new GetSetMethodNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $options = json_decode($serializer->serialize($adSearch, 'json'), true);

            return $this->redirectToRoute(
                'viteloge_frontend_ad_search',
                $options,
                301
            );
        }

        /**
         * Search from a query stats.
         * Cache is set from set last timestamp
         *
         * @Route(
         *     "/search/from/querystats/{id}",
         *     requirements={
         *         "id"="\d+"
         *     },
         *     name="viteloge_frontend_ad_searchfromquerystats"
         * )
         * @Cache(lastModified="queryStats.getUpdateAt()", ETag="'QueryStats' ~ queryStats.getId() ~ queryStats.getTimestamp()")
         * @ParamConverter("queryStats", class="VitelogeCoreBundle:QueryStats", options={"id" = "id"})
         * @Method({"GET","PUT"})
         */
        public function searchFromQueryStatsAction(Request $request, QueryStats $queryStats) {
            $em = $this->getDoctrine()->getManager();
            $queryStats->setCount($queryStats->getCount()+1);
            $em->persist($queryStats);
            $em->flush();

            $adSearch = new AdSearch();
            $adSearch->setTransaction($queryStats->getTransaction());
            $adSearch->setWhere($queryStats->getInseeCity()->getId());
            $adSearch->setWhat(ucfirst($queryStats->getType()));
            $adSearch->setRooms($queryStats->getRooms());
            $adSearch->setLocation($queryStats->getInseeCity()->getLocation());

            // transform object to array in order to through it to url
            $encoders = array(new JsonEncoder());
            $normalizers = array(new GetSetMethodNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $options = json_decode($serializer->serialize($adSearch, 'json'), true);
            $options['qs'] = $queryStats->getId();

            return $this->redirectToRoute(
                'viteloge_frontend_ad_search',
                $options,
                301
            );
        }

        /**
         * Show a carousel ads.
         * Ajax call, so we can set a public cache
         *
         * @Route(
         *     "/carousel/",
         *     requirements={
         *         "limit" = "\d+"
         *     },
         *     defaults={
         *         "limit" = "9"
         *     },
         *     name="viteloge_frontend_ad_carousel"
         * )
         * @Route(
         *     "/carousel/{limit}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit" = "9"
         *     },
         *     name="viteloge_frontend_ad_carousel_limited"
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Route(options={"expose"=true})
         * @Template("VitelogeFrontendBundle:Ad:carousel.html.twig")
         */
        public function carouselAction(Request $request, $limit) {
            $adSearch = new AdSearch();
            $adSearch->handleRequest($request);

            $transaction = $adSearch->getTransaction();
            $elasticaManager = $this->container->get('fos_elastica.manager');
            $repository = $elasticaManager->getRepository('VitelogeCoreBundle:Ad');
            $transaction = (!empty($transaction)) ? $transaction : 'default';
            $ads = $repository->search($adSearch, $limit);

            return array(
                'transaction' => $transaction,
                'ads' => $ads
            );
        }

        /**
         * Redirect to the hosted page.
         * There are no header information so we could set a good cache
         *
         * @Route(
         *      "/redirect/{id}",
         *      requirements={
         *          "id"="\d+"
         *      }
         * )
         * @Cache(lastModified="ad.getUpdatedAt()", ETag="'Ad' ~ ad.getId() ~ ad.getUpdatedAt().getTimestamp()")
         * @Method({"GET"})
         * @ParamConverter("ad", class="VitelogeCoreBundle:Ad", options={"id" = "id"})
         * @Template("VitelogeFrontendBundle:Ad:redirect.html.twig")
         */
        public function redirectAction(Request $request, Ad $ad) {
            $helper = $this->container->get('viteloge_frontend.ad_helper');
            $description = $helper->slugigy($ad,true);
            return $this->redirect($this->generateUrl('viteloge_frontend_agency_view', array('id'=>'0-'.$ad->getId(),'description' => $description)));

        }


    }


}
