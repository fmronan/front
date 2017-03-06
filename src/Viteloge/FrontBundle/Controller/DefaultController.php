<?php

namespace Viteloge\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Viteloge\CoreBundle\SearchEntity\Ad as AdSearch;
use Viteloge\FrontendBundle\Form\Type\AdSearchType;
use Viteloge\FrontendBundle\Controller\DefaultController as BaseController;


/**
* @Route("/")
*/
class DefaultController extends BaseController
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
         * @Template("VitelogeFrontBundle:Default:index.html.twig")
         */
    public function indexAction(Request $request)
    {
    	return parent::indexAction( $request);
    }

}
