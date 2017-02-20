<?php

namespace Viteloge\FrontendBundle\Controller {

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Cookie;
    use Pagerfanta\Pagerfanta;
    use Pagerfanta\Adapter\ArrayAdapter;
    use Pagerfanta\Adapter\DoctrineORMAdapter;
    use Viteloge\CoreBundle\Entity\Ad;
    use Viteloge\CoreBundle\SearchEntity\Ad as AdSearch;
    use Viteloge\FrontendBundle\Form\Type\AdSearchType;

    /**
     * Note: This should be the search ad controller
     * @Route("/prefer")
     */
    class PreferController extends Controller {


        /**
         * Ad favorie.
         *
         *
         * @Route(
         *     "/favourite/{id}",
         *     requirements={
         *         "id"="\d+"
         *     },
         *     name="viteloge_frontend_prefer_favourite"
         * )
         * @Route(options={"expose"=true})
         * @Method({"GET"})
         */
        public function favorieAction(Request $request, Ad $ad) {
            if($request->isXmlHttpRequest()){
                $time =time() + (3600 * 24 * 365);
                $cookies = $request->cookies;
            if ($cookies->has('viteloge_favorie')){
                    $cookie_favorie = $cookies->get('viteloge_favorie').'#$#'.$ad->getId();
            }else{
                $cookie_favorie = $ad->getId();
            }
            $response = new Response();
            $response->headers->setCookie(new Cookie('viteloge_favorie', $cookie_favorie,$time));
                return  $response;

            }else{
             throw new \Exception("Erreur");
            }

        }

        /**
         * Show latest ads for a request
         * Ajax call so we can have a public cache
         *
         * @Route(
         *     "/latest/{limit}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit" = "9"
         *     },
         *     name="viteloge_frontend_ad_latest"
         * )
         * @Route(
         *     "/latest/",
         *     defaults={
         *         "limit" = "9"
         *     },
         *     name="viteloge_frontend_prefer_latest"
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Ad:latest.html.twig")
         */
        public function latestAction(Request $request, $limit) {
            $adSearch = new AdSearch();
            $adSearch->handleRequest($request);
            // Save session
            $session = $request->getSession();
            $elasticaManager = $this->container->get('fos_elastica.manager');
            $repository = $elasticaManager->getRepository('VitelogeCoreBundle:Ad');
            $ads = $repository->search($adSearch, $limit);
            $session->set('resultAd', $ads);

              if($request->query->get('transaction') == 'V'){
               $session->set('totalResultVente',count($ads));
            }else{
              $session->set('totalResult',count($ads));
             }
            return array(
                'transaction' => $adSearch->getTransaction(),
                'cityName' => $request->query->get('cityName'),
                'ads' => $ads
            );
        }

        /**
         * Show latest ads (use in home)
         * Ajax call so we can have a public cache
         *
         * @Route(
         *     "/home/latest/{limit}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit" = "5"
         *     },
         *     name="viteloge_frontend_prefer_latest_limited"
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Ad:latest_home.html.twig")
         */
        public function latesthomeAction(Request $request, $limit) {

            $adSearch = new AdSearch();
            $session = $request->getSession();
            $elasticaManager = $this->container->get('fos_elastica.manager');
            $repository = $elasticaManager->getRepository('VitelogeCoreBundle:Ad');
            $ads = $repository->search($adSearch, 24);
            // Save session
            $session = $request->getSession();
            $session->set('resultAd', $ads);
            $session->remove('request');
            return array(
                'ads' => $ads
            );
        }

        /**
         * Show latest ads in list page
         *
         * @Route(
         *     "/last/{page}/{limit}",
         *     requirements={
                   "page"="\d+",
         *         "limit"="\d+"
         *     },
         *     defaults={
                   "page" = "1",
         *         "limit" = "24"
         *     },
         *     name="viteloge_frontend_prefer_latest_list"
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Ad:search_response.html.twig")
         */
        public function latestListAction(Request $request,$page,$limit) {
            $translated = $this->get('translator');
            $adSearch = new AdSearch();
            $adSearch->handleRequest($request);
            $form = $this->createForm(AdSearchType::class, $adSearch);

            $description = 'Les dernières annonces immobilières de viteloge';

            $elasticaManager = $this->container->get('fos_elastica.manager');
            $repository = $elasticaManager->getRepository('VitelogeCoreBundle:Ad');
            $pagination = $repository->searchPaginated($form->getData());


            // pager
            $pagination->setMaxPerPage($limit);
            $pagination->setCurrentPage($page);
            $seoPage = $this->container->get('sonata.seo.page');
            // SEO
            $canonicalLink = $this->get('router')->generate(
                $request->get('_route'),
                $request->get('_route_params'),
                true
            );

            $this->get('viteloge_frontend_generate.breadcrump')->genereBreadcrump(array('last'=>'breadcrumb.last'));
            $breadcrumbTitle  = $translated->trans('viteloge.frontend.lastadd');
            $seoPage
                ->setTitle($breadcrumbTitle.' - '.$translated->trans('viteloge.frontend.ad.search.title'))
                ->addMeta('name', 'robots', 'noindex, follow')
                ->addMeta('name', 'description', $description)
                ->addMeta('property', 'og:title', $seoPage->getTitle())
                ->addMeta('property', 'og:type', 'website')
                ->addMeta('property', 'og:url',  $canonicalLink)
                ->addMeta('property', 'og:description', $breadcrumbTitle.' - '.$translated->trans('viteloge.frontend.ad.search.description'))
                ->setLinkCanonical($canonicalLink)
            ;

            // Save session
            $session = $request->getSession();
            $session->set('totalResult',$pagination->getNbResults());
            $session->set('resultAd',$pagination->getCurrentPageResults());
            $session->remove('request');

            return array(
                'form' => $form->createView(),
                'ads' => $pagination->getCurrentPageResults(),
                'pagination' => $pagination,

            );
        }

        /**
         * News suggestion
         * Ajax call so we can have public cache
         *
         * @Route(
         *     "/suggest/new/{limit}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit" = "3"
         *     },
         *     name="viteloge_frontend_prefer_suggestnew_limited"
         * )
         * @Route(
         *     "/suggest/new/",
         *     defaults={
         *         "limit" = "3"
         *     },
         *     name="viteloge_frontend_ad_suggestnew"
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Ad:suggestNew.html.twig")
         */
        public function suggestNewAction(Request $request, $limit) {
            $em = $this->getDoctrine()->getManager();
            $queryBuilder = $em->createQueryBuilder()
                ->select('ad')
                ->from('VitelogeCoreBundle:Ad', 'ad')
                ->where('ad.agencyId = :agencyId')
                ->setParameter('agencyId', Ad::AGENCY_ID_NEW)
                ->orderBy('ad.createdAt', 'DESC')
            ;

            $adapter = new DoctrineORMAdapter($queryBuilder, true, false);
            $pagination = new Pagerfanta($adapter);
            $pagination->setCurrentPage(1);
            $pagination->setMaxPerPage($limit);

            return array(
                'count' => $pagination->getNbResults(),
                'ads' => $pagination->getCurrentPageResults()
            );
        }

         /**
         * Show latest ads with type and transaction(use in home)
         * Ajax call so we can have a public cache
         *
         * @Route(
         *     "/type/latest/{transaction}/{type}/{limit}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit" = "5"
         *     },
         *     name="viteloge_frontend_prefer_latest_transaction_type_limited"
         * )
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Ad:fragment/ad_list.html.twig")
         */
        public function mostSearchedAction(Request $request,$transaction,$type, $limit=5) {
            $repository = $this->getDoctrine()
                ->getRepository('VitelogeCoreBundle:Ad');
            $ads = $repository->findByTransactionandType($transaction,$type, $limit);
            return $this->render(
                'VitelogeFrontendBundle:Ad:fragment/ad_list.html.twig',
                array(
                    'ads' => $ads,
                    'transaction' => $transaction,
                    'type' => $type,
                )
            );
        }

        /**
         * remove the favourite list.
         *
         *
         * @Route(
         *     "/remove/favourite/{id}",
         *     name="viteloge_frontend_favourite_remove",
         *     requirements={
         *         "limit"="\d+"
         *     },
         * )
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Ad:favourite.html.twig")
         */
        public function removeFavouriteAction(Request $request,$id ) {
           $translated = $this->get('translator');
           $session = $request->getSession();
           $requestSearch = $session->get('request');
            // Form
            $adSearch = new AdSearch();
            $adSearch->handleRequest($requestSearch);
            $form = $this->createForm(AdSearchType::class, $adSearch);

            // Breadcrumbs
            $this->get('viteloge_frontend_generate.breadcrump')->genereBreadcrump(array('viteloge_frontend_user_index'=>'breadcrumb.user','last'=>'breadcrumb.favourite'));
               $cookies = $request->cookies;
            if ($cookies->has('viteloge_favorie')){
                $info_cookies_favorie = explode('#$#', $cookies->get('viteloge_favorie')) ;
                // on supprime l'id du cookies
                unset($info_cookies_favorie[array_search($id, $info_cookies_favorie)]);

                $repository = $this->getDoctrine()->getRepository('VitelogeCoreBundle:Ad');
             $ads = $repository->findById($info_cookies_favorie);
             // on reconstruit le cookie
             $cookies = $request->cookies;
                   $cookie_favorie = '';
                foreach ($info_cookies_favorie as $key => $value) {
                    if($key == 0){
                        $cookie_favorie = $value;
                    }else{
                      $cookie_favorie .= '#$#'.$value;
                    }

                }


            $response = new Response();
            $response->headers->setCookie(new Cookie('viteloge_favorie', $cookie_favorie));
            $TitleName =$translated->trans('breadcrumb.favourite', array(), 'breadcrumbs');
             // SEO
            $canonicalLink = $this->get('router')->generate(
                $request->get('_route'),
                $request->get('_route_params'),
                true
            );
            $seoPage = $this->container->get('sonata.seo.page');
            $seoPage
                ->setTitle($TitleName)
                ->addMeta('name', 'robots', 'noindex, follow')
                ->addMeta('property', 'og:title', $seoPage->getTitle())
                ->addMeta('property', 'og:type', 'website')
                ->addMeta('property', 'og:url',  $canonicalLink)
                ->setLinkCanonical($canonicalLink)
            ;


            return $this->render('VitelogeFrontendBundle:Ad:favourite.html.twig',array(
                'form' => $form->createView(),
                'ads' => $ads
            ), $response);

            }else{
               return $this->redirectToRoute(
                    'fos_user_profile_show');


            }


            }

        /**
         * view the favourite list.
         *
         *
         * @Route(
         *     "/list/favourite/$page/$limit",
         *    requirements={
         *         "page"="\d+",
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "page"=1,
         *         "limit"="25"
         *     },
         *     name="viteloge_frontend_favourite_list"
         * )
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Ad:favourite.html.twig")
         */
        public function listFavouriteAction(Request $request, $page, $limit) {
           $translated = $this->get('translator');

            // Breadcrumbs
            $TitleName =$translated->trans('breadcrumb.favourite', array(), 'breadcrumbs');
            $this->get('viteloge_frontend_generate.breadcrump')->genereBreadcrump(array('viteloge_frontend_user_index'=>'breadcrumb.user','last'=>'breadcrumb.favourite'));
               $cookies = $request->cookies;
            if ($cookies->has('viteloge_favorie')){
                $info_cookies_favorie = explode('#$#', $cookies->get('viteloge_favorie')) ;
                $repository = $this->getDoctrine()->getRepository('VitelogeCoreBundle:Ad');
             $ads = $repository->findById($info_cookies_favorie);

             // SEO
            $canonicalLink = $this->get('router')->generate(
                $request->get('_route'),
                $request->get('_route_params'),
                true
            );
            $seoPage = $this->container->get('sonata.seo.page');
            $seoPage
                ->setTitle($TitleName)
                ->addMeta('name', 'robots', 'noindex, follow')
                ->addMeta('property', 'og:title', $seoPage->getTitle())
                ->addMeta('property', 'og:type', 'website')
                ->addMeta('property', 'og:url',  $canonicalLink)
                ->setLinkCanonical($canonicalLink)
            ;

            $adapter = new ArrayAdapter($ads);
            $pagination = $this->getPagination($request,$adapter,$limit,$page);
            return array(
                'ads' => $pagination->getCurrentPageResults(),
                'pagination' => $pagination,
            );

            }else{
               return $this->redirectToRoute(
                    'fos_user_profile_show');
            }


            }

            /**
            *
            *
            *
            */
            private function getPagination($request,$adapter,$limit,$page){
            $pagination = new Pagerfanta($adapter);
            // --
            // pager
            $pagination->setMaxPerPage($limit);
            $pagination->setCurrentPage($page);
            $session = $request->getSession();
            $session->set('currentPage',$pagination->getCurrentPage());
            $session->set('resultAd',$pagination->getCurrentPageResults());
            $session->set('totalResult',$pagination->getNbResults());
            return $pagination;
            }

    }


}
