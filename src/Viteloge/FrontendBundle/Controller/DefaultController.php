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
        $this->container->get('viteloge_frontend_generate.seo')->genereSeo('index, follow',$translated->trans('viteloge.frontend.default.index.title'),$translated->trans('viteloge.frontend.default.index.description'),$canonicalLink);
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
            return array(
                'count' => $count,
                'newad'=> $newad,
                'form' => $form->createView(),
                'flash' =>'',
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
           return $this->render('VitelogeFrontendBundle:Base:headerSearch.html.twig',array(
                'form' => $form->createView(),
            ));
         }

}
