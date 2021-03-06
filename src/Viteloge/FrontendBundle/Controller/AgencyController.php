<?php

namespace Viteloge\FrontendBundle\Controller {

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Viteloge\CoreBundle\Entity\Ad;
    use Viteloge\CoreBundle\Entity\Infos;
    use Viteloge\CoreBundle\Entity\Statistics;
    use Viteloge\CoreBundle\SearchEntity\Ad as AdSearch;
    use Viteloge\FrontendBundle\Form\Type\AdSearchType;

    /**
     * Note: this controller to have a short route name
     * @Route("/announcement")
     */
    class AgencyController extends Controller {

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
         * @Template("VitelogeFrontendBundle:Ad:redirect_new.html.twig")
         */
        public function viewAction(Request $request,$id, $description) {
            $routeName = $request->get('_route');
            $infos = explode('-', $description);
            $em = $this->getDoctrine()->getManager();
            $id= explode('-', $id);
            $ad = $em->getRepository('VitelogeCoreBundle:Ad')->find($id[1]);

            $session = $request->getSession();
            if($routeName == 'viteloge_frontend_agency_lastview'){
               $ads = $session->get('resultView');
            }else{
               $ads = $session->get('resultAd');
            }

            $veriftotal = $session->get('totalResultVente');

            if($request->query->get('transaction') == 'V' && !is_null($veriftotal)){
              $total = $session->get('totalResultVente');
            }else{
              $total = $session->get('totalResult');
              $a = ($session->get('currentPage')-1) * 25;
              $b = $total - ($a);
              if($b > 25){
                $total = 25;
              }else{
                $total = $b;
              }
            }

            $search = $session->get('request');
            //si on atteind le nbs max de resultat en session on relance la recherche
            // Form
            $adSearch = new AdSearch();
            $adSearch->handleRequest($search);

            if(!isset($ad)){
               $options = array(
                'sort' => array(
                    'isCapital' => array( 'order' => 'desc' ),
                    'population' => array( 'order' => 'desc' )
                )
            );

            $search = $infos[1];
            $search = \Elastica\Util::escapeTerm($search);
            $index = $this->container->get('fos_elastica.finder.viteloge.inseeCity');
            $searchQuery = new \Elastica\Query\QueryString();
            $searchQuery->setParam('query', $search);
            $cities = $index->find($searchQuery, $options);
                        return $this->redirectToRoute(
                            'viteloge_frontend_glossary_showcity',
                            array('name' => $cities[0]->getName(),
                                  'id' => $cities[0]->getId()
                              ));

            }
            $form = $this->createForm(AdSearchType::class, $adSearch);

            // SEO
            $rewriteParam = $request->get('_route_params');

              $rewriteParam['id'] = '0-'.$ad->getId();

            $canonicalLink = $this->get('router')->generate(
                $request->get('_route'),
                $rewriteParam,
                true
            );

            $helper = $this->container->get('viteloge_frontend.ad_helper');
            $title = $helper->titlify($ad,true);
            $filters = $this->get('twig')->getFilters();
            $callable = $filters['truncate']->getCallable();
            $description = strtolower($callable($this->get('twig'), $ad->getDescription(), self::DESCRIPTION_LENGHT));
            $this->container->get('viteloge_frontend_generate.seo')->genereCanonicalSeo('index, follow',$title,$description,$canonicalLink);
            // --

            $forbiddenUA = array(
                'yakaz_bot' => 'YakazBot/1.0',
                'mitula_bot' => 'java/1.6.0_26'
            );
            $forbiddenIP = array(

            );
            $ua = $request->headers->get('User-Agent');
            $ip = $request->getClientIp();

            // log redirect
            if (!in_array($ua, $forbiddenUA) && !in_array($ip, $forbiddenIP)) {
                $statistics = new Statistics();
                $statistics->setIp($ip);
                $statistics->setUa($ua);
                $statistics->initFromAd($ad);
                $em = $this->getDoctrine()->getManager();
                $em->persist($statistics);
                $em->flush();
            }

            $favorie = $this->get('viteloge_frontend_generate.cookies')->searchFav($ad);
            $response = $this->get('viteloge_frontend_generate.cookies')->generateView($ad);

            $left = $id[0]-1;
            $right = $id[0]+1;
            $verifurl= $this->verifurlAction($ad->getUrl());
            return $this->render('VitelogeFrontendBundle:Ad:redirect_new.html.twig',array(
                'form' => $form->createView(),
                'ad' => $ad,
                'ads'=> $ads,
                'left' => $left,
                'right' => $right,
                'clef' => $id[0],
                'favorie' => $favorie,
                'redirect' => $verifurl,
                'total' => $total
            ), $response);
        }

        private function verifUrlAction($url){
            $error=false;
            $urlhere = $url;
            $ch = curl_init();
            $options = array(
                CURLOPT_URL            => $urlhere,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_AUTOREFERER    => true,
                CURLOPT_CONNECTTIMEOUT => 120,
                CURLOPT_TIMEOUT        => 120,
                CURLOPT_MAXREDIRS      => 10,);
            curl_setopt_array($ch, $options);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch);
            $headers=substr($response, 0, $httpCode['header_size']);
            if(strpos($headers, 'X-Frame-Options: deny')>-1 || strpos($headers, 'X-Frame-Options: SAMEORIGIN')>-1) {
                $error=true;
            }
            $error = $this->exclusionUrlAction($error,$url);
            $httpcode= curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $error;
        }


        public function exclusionUrlAction($error,$url){
            $redirectUrl = array(
                'century' => 'https://www.century21.fr',
                'century21' => 'http://www.century21.fr',
                'paruvendu' => 'http://www.paruvendu.fr/',
                'paruvendus' => 'https://www.paruvendu.fr/',
            );
            $verifurl = explode('://', $url);
            $baseurl = explode('/', $verifurl[1]);
            $newurl = $verifurl[0].'://'.$baseurl[0];

            if(in_array($newurl, $redirectUrl)){
                $error=true;
            }
            return $error;
        }


        /**
        * init for Infos
        *
        *
        *
        */
        private function initInfoAction($request,$ad){
            $ua = $request->headers->get('User-Agent');
            $ip = $request->getClientIp();
            $contact = new Infos();
            $contact->setIp($ip);
            $contact->setUa($ua);
            $contact->initFromAd($ad);
            return $contact;
        }

        /**
         * find surtax phone.
         *
         *
         * @Route(
         *     "/phone/surtax/{id}",
         *     requirements={
         *        "id"="\d+",
         *     },
         *     name="viteloge_frontend_agency_phone"
         * )
         * @Method({"POST"})
         * @ParamConverter("ad", class="VitelogeCoreBundle:Ad", options={"id" = "id"})
         * @Route(options={"expose"=true})
         * @Template("VitelogeFrontendBundle:Ad:fragment/btn_phone.html.twig")
         */
       public function getNumSurtaxeAction(Request $request,Ad $ad)
        {
          if($request->isXmlHttpRequest()){
            //on cherche le numero de l'agence avec son $id
            $em = $this->getDoctrine()->getManager();
            $agence = $em->getRepository('VitelogeCoreBundle:Agence')->find($ad->getAgencyId());
            if(!empty($agence)) $tel = $agence->getTel();
            $contact = $this->initInfoAction($request,$ad);
            if(isset($tel) && !empty($tel)){
            $contact->setGenre('dempandephone');
            }else{
              $contact->setGenre('phoneempty');
            }
                $em = $this->getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();
            $response = $this->get('viteloge_frontend_generate.phone')->getPhoneNumber($tel,$ad);
            return $response;
            }else{
             throw new \Exception("Erreur");
            }
        }


        /**
         * call surtax phone.
         *
         *
         * @Route(
         *     "/phone/call/{id}",
         *     requirements={
         *        "id"="\d+",
         *     },
         *     name="viteloge_frontend_agency_call"
         * )
         * @Method({"POST"})
         * @Route(options={"expose"=true})
         * @ParamConverter("ad", class="VitelogeCoreBundle:Ad", options={"id" = "id"})
         */
       public function callPhoneAction(Request $request,Ad $ad)
        {
          if($request->isXmlHttpRequest()){
                        $contact = $this->initInfoAction($request,$ad);
                        $contact->setGenre('appelle');
                        $contact->initFromAd($ad);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($contact);
                        $em->flush();

                 $response = new JsonResponse();
            return $response->setData(array());
            }else{
             throw new \Exception("Erreur");
            }
        }

         /**
         * Display the last ad (Used in homepage)
         * Ajax call so we could have shared public cache
         *
         * @Route(
         *     "/latest/{limit}",
         *     requirements={
         *         "limit"="\d+"
         *     },
         *     defaults={
         *         "limit" = "5"
         *     },
         *     name="viteloge_frontend_agency_latest_limited"
         * )
         * @Route(
         *      "/latest/",
         *      requirements={
         *         "limit"="\d+"
         *      },
         *      defaults={
         *         "limit" = "10"
         *      },
         *      name="viteloge_frontend_agency_latest"
         * )
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Agency:latest.html.twig")
         */
         public function latestViewAction(Request $request, $limit) {
            $em = $this->getDoctrine()->getManager();
            $ads = $em->getRepository('VitelogeCoreBundle:Statistics')->findBy(array(), array('date' => 'DESC'),$limit);
            return array(
                'ads' => $ads
            );
        }

        /**
         * ajax last search (Home).
         *
         *
         * @Route(
         *     "/last/view",
         *     name="viteloge_frontend_agency_last_view"
         * )
         * @Method({"POST","GET"})
         * @Route(options={"expose"=true})
         */
        public function lastSearchAction(Request $request)
        {
          if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $ads = $em->getRepository('VitelogeCoreBundle:Statistics')->findBy(array(), array('date' => 'DESC'),10);
            return $this->reponseSearchAction($ads);

            }else{
             throw new \Exception("Erreur");
            }
        }


        public function reponseSearchAction($ads)
        {

            return $this->render(
                'VitelogeFrontendBundle:Agency:Render/ajax_latest.html.twig',
                array(
                    'ads' => $ads
                )
            );

        }


    }


}
