<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Sonata\SeoBundle\SonataSeoBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new JMS\I18nRoutingBundle\JMSI18nRoutingBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            //new JMS\TranslationBundle\JMSTranslationBundle(),
            new EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle(),
            new Presta\SitemapBundle\PrestaSitemapBundle(),
            new Genemu\Bundle\FormBundle\GenemuFormBundle(), // used in Estimation
            //new AppBundle\AppBundle(),
            new Viteloge\FrontendBundle\VitelogeFrontendBundle(),
            new Viteloge\CoreBundle\VitelogeCoreBundle(),
            new Viteloge\InseeBundle\VitelogeInseeBundle(),
            new Viteloge\OAuthBundle\VitelogeOAuthBundle(),
            new Viteloge\UserBundle\VitelogeUserBundle(),
            new Viteloge\EstimationBundle\VitelogeEstimationBundle(),
            new Viteloge\TwigBundle\VitelogeTwigBundle(),
            new Viteloge\FrontBundle\VitelogeFrontBundle(),
            new Viteloge\UsersBundle\VitelogeUsersBundle(),
            new Viteloge\EstimBundle\VitelogeEstimBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
