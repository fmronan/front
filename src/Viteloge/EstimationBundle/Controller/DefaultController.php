<?php

namespace Viteloge\EstimationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Viteloge\CoreBundle\Entity\Estimate;
use Viteloge\EstimationBundle\Form\Type\EstimationType;
use Viteloge\EstimationBundle\Form\Type\IntroEstimationType;
use Viteloge\EstimationBundle\Form\Type\ContactEstimationType;


/**
 * @Route("prix-immobilier/")
 */
class DefaultController extends Controller {


    /**
     * @Route(
     *      "votre-estimation/",
     *      options = {
     *          "i18n" = true,
     *          "vl_sitemap" = {
     *              "title" = "viteloge.estimation.default.index.title",
     *              "description" = "viteloge.estimation.default.index.description",
     *              "changefreq" = "monthly",
     *              "priority" = "0.8"
     *          }
     *     },
     * )
     * @Method("GET")
     * @Template()
     */
    public function indexAction( Request $request ) {
        $translated = $this->get('translator');
        // Breadcrumbs
        $this->get('viteloge_frontend_generate.breadcrump')->genereBreadcrump(array('last'=>'breadcrumb.estimate'));
        // SEO
        $canonicalLink = $this->get('router')->generate($request->get('_route'), array(), true);
        $this->container->get('viteloge_frontend_generate.seo')->genereCanonicalSeo('index, follow',$translated->trans('viteloge.estimation.default.index.title'),$translated->trans('viteloge.estimation.default.index.description'),$canonicalLink);
        // --
        $estimate = new Estimate();
        $form = $this->createForm( EstimationType::class, $estimate );
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("votre-estimation/")
     * @Method("POST")
     * @Template("VitelogeEstimationBundle:Default:index.html.twig")
     */
    public function indexPostAction( Request $request ) {
        $translated = $this->get('translator');
        // Breadcrumbs
        $this->get('viteloge_frontend_generate.breadcrump')->genereBreadcrump(array('last'=>'breadcrumb.estimate'));
        // SEO
        $canonicalLink = $this->get('router')->generate('viteloge_estimation_default_index', array(), true);
        $this->container->get('viteloge_frontend_generate.seo')->genereCanonicalSeo('noindex, nofollow',$translated->trans('viteloge.estimation.default.index.title'),$translated->trans('viteloge.estimation.default.index.description'),$canonicalLink);
        // --
        $estimate = new Estimate();
        $form = $this->createForm(EstimationType::class, $estimate );
        $post_is_intro = false;
        if ( $request->query->get( 'intro', false ) ) {
            $post_is_intro = true;

            $form_intro = $this->createForm( IntroEstimationType::class, $estimate );
            $form_intro->handleRequest( $request );
        }

        $form = $this->createForm( EstimationType::class, $estimate, array(
            "action" => $this->generateUrl( 'viteloge_estimation_default_indexpost')
        ) );

        if ( ! $post_is_intro ) {
            $form->handleRequest( $request );

            if ( $form->isValid() ) {
                $handler = $this->get('viteloge_estimation.estimate.handler');
                $handler->save( $estimate );

                return $this->redirect(
                    $this->generateUrl(
                        'viteloge_estimation_default_resultat',
                        array( 'id' => $estimate->getId() )
                    )
                );
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("votre-estimation/resultat/{id}")
     * @Method("GET")
     * @Template()
     */
    public function resultatAction(Request $request, Estimate $estimate ) {
        $translated = $this->get('translator');
        // Breadcrumbs
        $this->get('viteloge_frontend_generate.breadcrump')->genereBreadcrump(array('viteloge_estimation_default_index'=>'breadcrumb.estimate','last'=>'breadcrumb.estimate.result'));
        // SEO
        $canonicalLink = $this->get('router')->generate($request->get('_route'), array('id' => $estimate->getId()), true);
        $this->container->get('viteloge_frontend_generate.seo')->genereCanonicalSeo('noindex, nofollow',$translated->trans('viteloge.estimation.default.resultat.title'),$translated->trans('viteloge.estimation.default.resultat.description'),$canonicalLink);
        // --

        $computer = $this->get( 'viteloge_estimation.estimate.computer' );

        $form = null;
        if ( ! $estimate->hasAgencyRequest() ) {
            $form = $this->createForm( ContactEstimationType::class, $estimate );
        }

        $result = $computer->estimate( $estimate );
        $debug = false;
        if ( $result ) {
            $debug = $result["debug"];
            unset( $result["debug"] );
        }

        return array(
            'computed_estimation' => $result,
            'debug_result' => $debug,
            'form' => $form ? $form->createView() : null
        );
    }

    /**
     * @Route("votre-estimation/resultat/{id}")
     * @Method("POST")
     * @Template("VitelogeEstimationBundle:Default:resultat.html.twig")
     */
    public function resultatContactAction( Request $request, Estimate $estimate ) {
        $translated = $this->get('translator');
        // Breadcrumbs
        $this->get('viteloge_frontend_generate.breadcrump')->genereBreadcrump(array('viteloge_estimation_default_index'=>'breadcrumb.estimate','last'=>'breadcrumb.estimate.result'));

        // SEO
        $canonicalLink = $this->get('router')->generate('viteloge_estimation_default_resultat', array('id' => $estimate->getId()), true);
        $this->container->get('viteloge_frontend_generate.seo')->genereCanonicalSeo('noindex, nofollow',$translated->trans('viteloge.estimation.default.resultat.title'),$translated->trans('viteloge.estimation.default.resultat.description'),$canonicalLink);
        // --

        $form = $this->createForm( ContactEstimationType::class, $estimate );

        $form->handleRequest( $request );

        if ( $form->isValid() ) {
            $handler = $this->get('viteloge_estimation.estimate.handler');
            $handler->save( $estimate );
            return $this->redirect(
                $this->generateUrl(
                    'viteloge_estimation_default_contact'
                )
            );
        }

        $computer = $this->get( 'viteloge_estimation.estimate.computer' );
        $result = $computer->estimate( $estimate );
        $debug = false;
        if ( $result ) {
            $debug = $result["debug"];
            unset( $result["debug"] );
        }

        return array(
            'computed_estimation' => $result,
            'debug_result' => $debug,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("votre-estimation/contact")
     * @Method("GET")
     * @Template()
     */
    public function contactAction(Request $request) {
        $translated = $this->get('translator');
        // Breadcrumbs
          $this->get('viteloge_frontend_generate.breadcrump')->genereBreadcrump(array('viteloge_estimation_default_index'=>'breadcrumb.estimate','last'=>'breadcrumb.estimate.contact'));

        // SEO
        $canonicalLink = $this->get('router')->generate($request->get('_route'), array(), true);
        $this->container->get('viteloge_frontend_generate.seo')->genereCanonicalSeo('noindex, nofollow',$translated->trans('viteloge.estimation.default.contact.title'),$translated->trans('viteloge.estimation.default.contact.description'),$canonicalLink);
        // --

        return array(

        );
    }


}
