<?php

namespace Viteloge\FrontendBundle\Services {

    use Symfony\Component\Translation\TranslatorInterface;
    use Symfony\Component\DependencyInjection\ContainerAwareInterface;
    use Symfony\Component\DependencyInjection\ContainerAwareTrait;
    use Symfony\Bundle\FrameworkBundle\Routing\Router;
    use Viteloge\InseeBundle\Entity\InseeDepartment;
    use Viteloge\InseeBundle\Entity\InseeCity;



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


        public function getClassicBreadcrump($inseeCity){

        $this->breadcrumbs = $this->initBreadcrump();

        if ($inseeCity->getInseeState()){
            $this->breadcrumbs->addItem(
                $inseeCity->getInseeState()->getFullname(),
                $this->router->generate('viteloge_frontend_glossary_showstate',
                    array(
                        'name' => $inseeCity->getInseeState()->getSlug(),
                        'id' => $inseeCity->getInseeState()->getId()
                    )
                )
            );
        }
        if ($inseeCity->getInseeDepartment()) {
            $this->breadcrumbs->addItem(
                $inseeCity->getInseeDepartment()->getFullname(),
                $this->router->generate('viteloge_frontend_glossary_showdepartment',
                    array(
                        'name' => $inseeCity->getInseeDepartment()->getSlug(),
                        'id' => $inseeCity->getInseeDepartment()->getId()
                    )
                )
            );
        }
        $this->breadcrumbs->addItem(
            $inseeCity->getFullname(),
            $this->router->generate('viteloge_frontend_glossary_showcity',
                array(
                    'name' => $inseeCity->getSlug(),
                    'id' => $inseeCity->getId()
                )
            )
        );

        return $this->breadcrumbs;
        }

       public function getDeptAndCityBreadcrump($inseeDepartment,$inseeCity,$transaction){
        $this->breadcrumbs = $this->initBreadcrump();
       if ($inseeDepartment instanceof InseeDepartment) {
                $breadcrumbTitle  = (!empty($transaction)) ? $this->translated->trans('ad.transaction.'.strtoupper($transaction[0])).' ' : '';
                $breadcrumbTitle .= $inseeDepartment->getFullname();
                $this->breadcrumbs->addItem(
                    $breadcrumbTitle,
                    $this->router->generate('viteloge_frontend_ad_search',
                        array(
                            'transaction' => $transaction,
                            'whereDepartment' => array($inseeDepartment->getId())
                        )
                    )
                );
            }
            if ($inseeCity instanceof InseeCity) {
                $breadcrumbTitle  = (!empty($transaction)) ? $this->translated->trans('ad.transaction.'.strtoupper($transaction[0])).' ' : '';
                $breadcrumbTitle .= $inseeCity->getFullname().' ('.$inseeCity->getInseeDepartment()->getId().')';
                $this->breadcrumbs->addItem(
                    $breadcrumbTitle,
                    $this->router->generate('viteloge_frontend_glossary_showcity',
                        array(
                            'name' => $inseeCity->getSlug(),
                            'id' => $inseeCity->getId()
                        )
                    )
                );
            }
            return $this->breadcrumbs;
        }

    }

}
