<?php

namespace Viteloge\FrontendBundle\Services {

    use Symfony\Component\Translation\TranslatorInterface;
    use Symfony\Component\DependencyInjection\ContainerAwareInterface;
    use Symfony\Component\DependencyInjection\ContainerAwareTrait;
    use Symfony\Bundle\FrameworkBundle\Routing\Router;



    class BreadcrumpFactory implements ContainerAwareInterface{
       use ContainerAwareTrait;
       /**
         *
         */
        protected $translated;

        /**
         *
         */
        protected $router;

        /**
         *
         */
        protected $breadcrumbs;

        /**
         *
         */
        public function __construct(TranslatorInterface $translated,Router $router,$breadcrumbs) {
            $this->translated = $translated;
            $this->router = $router;
            $this->breadcrumbs = $breadcrumbs;
        }

        public function initBreadcrump(){
            $this->breadcrumbs->addItem(
                $this->translated->trans('breadcrumb.home', array(), 'breadcrumbs'),
                $this->router->generate('viteloge_frontend_homepage')
            );
            return $this->breadcrumbs;

        }

        public function genereBreadcrump($infos){
           $this->breadcrumbs = $this->initBreadcrump();
           foreach ($infos as $key => $info) {
               if($key != 'last'){
                 $this->breadcrumbs->addItem(
                $this->translated->trans($info, array(), 'breadcrumbs'),
                $this->router->generate($key)
                );

               }else{
                    $this->breadcrumbs->addItem(
                $this->translated->trans($info, array(), 'breadcrumbs')
               );


               }
           }
           return $this->breadcrumbs;

        }

        public function getBreadcrump($infos){
           $this->breadcrumbs = $this->initBreadcrump();
           foreach ($infos as $key => $info) {
               if($key != 'last'){
                 $this->breadcrumbs->addItem(
                  $this->translated->trans($info, array(), 'breadcrumbs'),
                  $key
                );

               }else{
                    $this->breadcrumbs->addItem(
                $this->translated->trans($info, array(), 'breadcrumbs')
               );


               }
           }
           return $this->breadcrumbs;

        }



    }

}
