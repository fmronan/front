<?php

namespace Viteloge\FrontendBundle\Services {


    use Symfony\Component\DependencyInjection\ContainerAwareInterface;
    use Symfony\Component\DependencyInjection\ContainerAwareTrait;


    class SeoFactory implements ContainerAwareInterface{
       use ContainerAwareTrait;

       private $seoPage;

       public function __construct($seoPage){

            $this->seoPage = $seoPage;

         }


        public function genereCanonicalSeo($index,$title,$description,$canonicalLink,$ogdescription=null,$ogtitle= null)
        {

            $this->seoPage
                ->setTitle($title)
                ->addMeta('name', 'robots', $index)
                ->addMeta('name', 'description', $description);
                if(is_null($ogtitle)){
                  $this->seoPage->addMeta('property', 'og:title', $this->seoPage->getTitle());
                }else{
                  $this->seoPage->addMeta('property', 'og:title', $ogtitle);
                }
                $this->seoPage->addMeta('property', 'og:type', 'website')
                        ->addMeta('property', 'og:url',  $canonicalLink);
                if(is_null($ogdescription)){
                  $this->seoPage->addMeta('property', 'og:description', $description);
                }else{
                  $this->seoPage->addMeta('property', 'og:description', $ogdescription);
                }

                $this->seoPage->setLinkCanonical($canonicalLink)
            ;

           return $this->seoPage;

        }

        public function genereSeo($index,$title,$description,$canonicalLink,$ogtitle= null,$ogdescription=null)
        {

            $this->seoPage
                ->setTitle($title)
                ->addMeta('name', 'robots', $index)
                ->addMeta('name', 'description', $description);
                if(is_null($ogtitle)){
                  $this->seoPage->addMeta('property', 'og:title', $this->seoPage->getTitle());
                }else{
                  $this->seoPage->addMeta('property', 'og:title', $ogtitle);
                }
                $this->seoPage->addMeta('property', 'og:type', 'website')
                        ->addMeta('property', 'og:url',  $canonicalLink);
                if(is_null($ogdescription)){
                  $this->seoPage->addMeta('property', 'og:description', $description);
                }else{
                  $this->seoPage->addMeta('property', 'og:description', $ogdescription);
                }
            ;

           return $this->seoPage;

        }



    }

}
