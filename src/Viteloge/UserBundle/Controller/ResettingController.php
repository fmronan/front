<?php

namespace Viteloge\UserBundle\Controller {

    use Symfony\Component\HttpFoundation\Request;
    use FOS\UserBundle\Controller\ResettingController as BaseController;



    class ResettingController extends BaseController {

        public function requestAction() {
            $translated = $this->get('translator');
            $requestStack =$this->get('request_stack');
            $request = $requestStack->getCurrentRequest();
            $route = $request->get('_route');
            // Breadcrumbs
            $breadcrumbs = $this->get('white_october_breadcrumbs');
            $breadcrumbs->addItem(
                $translated->trans('breadcrumb.home', array(), 'breadcrumbs'),
                $this->get('router')->generate('viteloge_frontend_homepage')
            );
            $breadcrumbs->addItem(
                $translated->trans('breadcrumb.user', array(), 'breadcrumbs'),
                $this->get('router')->generate('viteloge_frontend_user_index')
            );
            $breadcrumbs->addItem(
                $translated->trans('breadcrumb.reset', array(), 'breadcrumbs')
            );
            // --

            // SEO
            $canonicalLink = $this->get('router')->generate($route, array(), true);
            $seoPage = $this->container->get('sonata.seo.page');
            $seoPage
                ->setTitle($translated->trans('viteloge.user.resetting.request.title'))
                ->addMeta('name', 'description', $translated->trans('viteloge.user.resetting.request.description'))
                ->addMeta('property', 'og:title', $translated->trans('viteloge.user.resetting.request.title'))
                ->addMeta('property', 'og:type', 'website')
                ->addMeta('property', 'og:url',  $canonicalLink)
                ->addMeta('property', 'og:description', $translated->trans('viteloge.user.resetting.request.description'))
                ->setLinkCanonical($canonicalLink)
            ;
            // --

            return parent::requestAction();
        }

        public function sendEmailAction(Request $request) {
            $translated = $this->get('translator');

            // Breadcrumbs
            $breadcrumbs = $this->get('white_october_breadcrumbs');
            $breadcrumbs->addItem(
                $translated->trans('breadcrumb.home', array(), 'breadcrumbs'),
                $this->get('router')->generate('viteloge_frontend_homepage')
            );
            $breadcrumbs->addItem(
                $translated->trans('breadcrumb.user', array(), 'breadcrumbs'),
                $this->get('router')->generate('viteloge_frontend_user_index')
            );
            $breadcrumbs->addItem(
                $translated->trans('breadcrumb.reset', array(), 'breadcrumbs')
            );
            // --

            // SEO
            $route = $request->get('_route');
            $canonicalLink = $this->get('router')->generate($route, array(), true);
            $seoPage = $this->container->get('sonata.seo.page');
            $seoPage
                ->setTitle($translated->trans('viteloge.user.resetting.request.title'))
                ->addMeta('name', 'description', $translated->trans('viteloge.user.resetting.request.description'))
                ->addMeta('property', 'og:title', $translated->trans('viteloge.user.resetting.request.title'))
                ->addMeta('property', 'og:type', 'website')
                ->addMeta('property', 'og:url',  $canonicalLink)
                ->addMeta('property', 'og:description', $translated->trans('viteloge.user.resetting.request.description'))
                ->setLinkCanonical($canonicalLink)
            ;
            // --

            return parent::sendEmailAction($request);
        }

        public function checkEmailAction(Request $request) {
            $translated = $this->get('translator');
            // SEO
            $route = $request->get('_route');
            $canonicalLink = $this->get('router')->generate($route, array(), true);
            $seoPage = $this->container->get('sonata.seo.page');
            $seoPage
                ->setTitle($translated->trans('viteloge.user.ask.request.title'))
                ->addMeta('name', 'description', $translated->trans('viteloge.user.resetting.request.description'))
                ->addMeta('property', 'og:title', $translated->trans('viteloge.user.resetting.request.title'))
                ->addMeta('property', 'og:type', 'website')
                ->addMeta('property', 'og:url',  $canonicalLink)
                ->addMeta('property', 'og:description', $translated->trans('viteloge.user.resetting.request.description'))
                ->setLinkCanonical($canonicalLink)
            ;
            // Breadcrumbs
            $breadcrumbs = $this->get('white_october_breadcrumbs');
            $breadcrumbs->addItem(
                $translated->trans('breadcrumb.home', array(), 'breadcrumbs'),
                $this->get('router')->generate('viteloge_frontend_homepage')
            );
            $breadcrumbs->addItem(
                $translated->trans('breadcrumb.user', array(), 'breadcrumbs'),
                $this->get('router')->generate('viteloge_frontend_user_index')
            );
            $breadcrumbs->addItem(
                $translated->trans('breadcrumb.reset', array(), 'breadcrumbs')
            );

            return parent::checkEmailAction($request);
        }

    }

}
