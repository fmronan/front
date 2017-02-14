<?php

namespace Viteloge\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Viteloge\CoreBundle\SearchEntity\Ad as AdSearch;
use Viteloge\FrontendBundle\Form\Type\AdSearchType;

/**
* @Route("/")
*/
class DefaultController extends Controller
{

         /**
         * Homepage
         * No cache
         *
         * @Route(
         *     "/",
         *     defaults={},
         *     name="viteloge_frontend_homepage",
         * )
         * @Method({"GET"})
         * @Template("VitelogeFrontendBundle:Default:index.html.twig")
         */
    public function indexAction(Request $request)
    {

    	$translated = $this->get('translator');
    	$canonicalLink = $this->get('router')->generate($request->get('_route'), array());
    	$seoPage = $this->container->get('sonata.seo.page');
    	$seoPage
                ->setTitle($translated->trans('viteloge.frontend.default.index.title'))
                ->addMeta('name', 'description', $translated->trans('viteloge.frontend.default.index.description'))
                ->addMeta('property', 'og:title', $translated->trans('viteloge.frontend.default.index.title'))
                ->addMeta('property', 'og:type', 'website')
                ->addMeta('property', 'og:url',  $canonicalLink)
                ->addMeta('property', 'og:description', $translated->trans('viteloge.frontend.default.index.description'))
            ;
        // This count is pretty faster than an elastic search count
            $repository = $this->getDoctrine()
                ->getRepository('VitelogeCoreBundle:Ad');
            $count = $repository->countByFiltered();
            $repository = $this->getDoctrine()
                ->getRepository('VitelogeCoreBundle:Ad');
            $newad = $repository->findNewAdLimit();

         // Form
            $entity = new AdSearch();
            $form = $this->createForm(AdSearchType::class, $entity);

         //   $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
            return array(
                'count' => $count,
                'newad'=> $newad,
                'form' => $form->createView(),
             //   'csrf_token' => $csrfToken,
            );
    }

    public function headerFormAction( Request $request ) {
            $session = $request->getSession();
            $requestSearch = $session->get('request');
            // Form
            $adSearch = new AdSearch();
          if(!is_null($requestSearch)){

           $adSearch->handleRequest($requestSearch);
          }
           $form = $this->createForm(AdSearchType::class, $adSearch);
          // $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
           return $this->render('VitelogeFrontendBundle:Base:headerSearch.html.twig',array(
                'form' => $form->createView(),
               // 'csrf_token' => $csrfToken,
            ));
         }

}
