<?php

namespace Viteloge\FrontendBundle\EventListener {

    use Symfony\Component\Routing\Route;
    use Symfony\Component\Routing\RouterInterface;
    use Doctrine\ORM\EntityManager;
    use Presta\SitemapBundle\Event\SitemapPopulateEvent;
    use Presta\SitemapBundle\EventListener\RouteAnnotationEventListener;
    use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
    use Viteloge\FrontendBundle\Component\Helper\AdHelper;
    use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

    class SitemapListener extends RouteAnnotationEventListener {

        const URL_LIMIT = 1000;

        /**
         *
         */
        protected $router;

        /**
         *
         */
        protected $entityManager;

        /**
         *
         */
        protected $helper;

        /**
         *
         */
        public function __construct(RouterInterface $router, EntityManager $entityManager, AdHelper $helper) {
            $this->router = $router;
            $this->entityManager = $entityManager;
            $this->helper = $helper;
        }

        /**
         * Should check $event->getSection() and then populate the sitemap
         * using $event->getGenerator()->addUrl(\Presta\SitemapBundle\Sitemap\Url\Url $url, $section)
         * if $event->getSection() is null or matches the listener's section
         *
         * @param SitemapPopulateEvent $event
         *
         * @throws \InvalidArgumentException
         * @return void
         */
        public function populateSitemap(SitemapPopulateEvent $event) {
            $section = $event->getSection();
            switch ($section) {
                case 'cities':
                    $this->addUrlsFromCities($event);
                    break;
                case 'queries':
                    $this->addUrlsFromQueries($event);
                    break;
                case 'ads':
                    $this->addUrlsFromAds($event);
                    break;
                case 'default':
                    $this->addUrlsFromRoutes($event);
                    break;
                default:
                    $this->addUrlsFromRoutes($event);
                    break;
            }
        }

        /**
         * @param SitemapPopulateEvent $event
         * @throws \InvalidArgumentException
         */
        private function addUrlsFromRoutes(SitemapPopulateEvent $event) {
            $collection = $this->router->getOriginalRouteCollection();
            foreach ($collection->all() as $name => $route) {
                $options = $this->getOptions($name, $route);
                if ($options) {
                    $event->getGenerator()->addUrl(
                        $this->getUrlConcrete($name, array(), $options),
                        $event->getSection() ? $event->getSection() : 'default'
                    );
                }
            }
        }

        /**
         * http://doctrine-orm.readthedocs.org/en/latest/reference/batch-processing.html
         */
        private function addUrlsFromCities(SitemapPopulateEvent $event) {
            $q = $this->entityManager->createQuery('select ic from VitelogeInseeBundle:InseeCity ic');
            $iterableResult = $q->iterate();
            $options = array(
                'priority' => 1,
                'changefreq' => UrlConcrete::CHANGEFREQ_DAILY,
                'lastmod' => new \DateTime()
            );
            $i = 0;
            $j = 0;
            $glossary_section = 'city_glossary_part_';
            $statistic_section = 'city_statistic_part_';
            $keyword_section = 'city_keyword_part_';
            foreach ($iterableResult as $key => $row) {
                $city = $row[0];
                if (!empty($city->getSlug())) {
                    $i++;
                    $parameters = array(
                        'name' => $city->getSlug(),
                        'id' => $city->getId()
                    );
                    $event->getGenerator()->addUrl(
                        $this->getUrlConcrete('viteloge_frontend_glossary_showcity', $parameters, $options),
                        $glossary_section.$j
                    );
                    $event->getGenerator()->addUrl(
                        $this->getUrlConcrete('viteloge_estimation_statistic_city', $parameters, $options),
                        $statistic_section.$j
                    );
                    $event->getGenerator()->addUrl(
                        $this->getUrlConcrete('viteloge_frontend_querystats_city', $parameters, $options),
                        $keyword_section.$j
                    );
                    if ($i % static::URL_LIMIT == 0) {
                        $j++;
                    }
                }
                $this->entityManager->detach($row[0]);
            }
        }

        /**
         * http://doctrine-orm.readthedocs.org/en/latest/reference/batch-processing.html
         */
        private function addUrlsFromQueries(SitemapPopulateEvent $event) {
            $q = $this->entityManager->createQuery('select qs from VitelogeCoreBundle:QueryStats qs');
            $iterableResult = $q->iterate();
            $options = array(
                'priority' => 1,
                'changefreq' => UrlConcrete::CHANGEFREQ_DAILY,
                'lastmod' => new \DateTime()
            );
            $i = 0;
            $j = 0;
            $ad_section = 'keyword_ad_part_';
            foreach ($iterableResult as $key => $row) {
                $query = $row[0];
                if (!empty($query->getSlug())) {
                    $i++;
                    $parameters = array(
                        'slug' => $query->getSlug()
                    );
                    $event->getGenerator()->addUrl(
                        $this->getUrlConcrete('viteloge_frontend_querystats_ad', $parameters, $options),
                        $ad_section.$j
                    );
                    if ($i % static::URL_LIMIT == 0) {
                        $j++;
                    }
                }
                $this->entityManager->detach($row[0]);
            }
        }

        /**
         * http://doctrine-orm.readthedocs.org/en/latest/reference/batch-processing.html
         */
        private function addUrlsFromAds(SitemapPopulateEvent $event){
            $q = $this->entityManager->createQuery('select ad from VitelogeCoreBundle:Ad ad');
            $iterableResult = $q->iterate();
            $options = array(
                'priority' => 1,
                'changefreq' => UrlConcrete::CHANGEFREQ_DAILY,
                'lastmod' => new \DateTime()
            );
            $i = 0;
            $j = 0;
            $ad_section = 'agency_ad_part_';
            foreach ($iterableResult as $key => $row) {
                $ad = $row[0];
                $i++;
                $description = $this->helper->slugigy($ad);
                $parameters = array(
                    'id' => '0-'.$ad->getId(),
                    'description' => $description,
                );
                $event->getGenerator()->addUrl(
                    $this->getUrlConcrete('viteloge_frontend_agency_view', $parameters, $options),
                    $ad_section.$j
                );
                if ($i % static::URL_LIMIT == 0) {
                    $j++;
                }
                $this->entityManager->detach($row[0]);
            }

        }

        /**
         * @param $name
         * @param  Route $route
         * @throws \InvalidArgumentException
         * @return array
         */
        public function getOptions($name, Route $route) {
            $option = $route->getOption('vl_sitemap');
            if ($option === null) {
                return null;
            }
            if (!filter_var($option, FILTER_VALIDATE_BOOLEAN) && !is_array($option)) {
                throw new \InvalidArgumentException('the sitemap option must be "true" or an array of parameters');
            }
            $options = array(
                'priority' => 1,
                'changefreq' => UrlConcrete::CHANGEFREQ_DAILY,
                'lastmod' => new \DateTime()
            );
            if (is_array($option)) {
                if (isset($option['lastmod'])) {
                    try {
                        $lastmod = new \DateTime($option['lastmod']);
                        $option['lastmod'] = $lastmod;
                    } catch (\Exception $e) {
                        throw new \InvalidArgumentException(
                            sprintf(
                                'The route %s has an invalid value "%s" specified for the "lastmod" option',
                                $name,
                                $option['lastmod']
                            )
                        );
                    }
                }
                $options = array_merge($options, $option);
            }
            return $options;
        }

        /**
         * @param $name
         * @param $parameters
         * @param $options
         * @return UrlConcrete
         * @throws \InvalidArgumentException
         */
        private function getUrlConcrete($name, $parameters, $options) {
            try {
                $url = new UrlConcrete(
                    $this->getRouteUri($name, $parameters),
                    $options['lastmod'],
                    $options['changefreq'],
                    $options['priority']
                );
                return $url;
            } catch (\Exception $e) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid argument for route "%s": %s',
                        $name,
                        $e->getMessage()
                    )
                );
            }
        }

        /**
         * @param $name
         * @param $parameters
         * @return string
         * @throws \InvalidArgumentException
         */
        private function getRouteUri($name, $parameters=array()) {
            // does the route need parameters? if so, we can't add it
            try {
                return $this->router->generate($name, $parameters, true);
            } catch (MissingMandatoryParametersException $e) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'The route "%s" cannot have the sitemap option because it requires parameters',
                        $name
                    )
                );
            }
        }
    }

}
