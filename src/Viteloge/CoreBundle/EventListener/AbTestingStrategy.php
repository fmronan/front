<?php

namespace Viteloge\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * AbTestingStrategy
 *
 * Event listener for handling the new account section rollout.  Once the test is
 * complete, this class can simply be removed (along with the entire old account bundle)
 */
class AbTestingStrategy
{
    const TEST_GROUP_COOKIE = 'account_rollout_group';
    const NEW_ACCOUNT_GROUP = 'account-new';
    const OLD_ACCOUNT_GROUP = 'account-old';

    /**
     * the following class variables are set in the config parameters.yml,
     * under the section 'account.section.rollout_settings'
     */

    // enables the rollout test
    private $enabled;

    // percentage of people (in the test locales) who see the new account section at any given time
    // a value of .1 would mean 10% of people (in the test locales) see new account section
    private $testWeight;

    // mapping of the account group names to their bundles that need to be loaded
    private $controllerGroupMapping = array(
        self::NEW_ACCOUNT_GROUP => 'VitelogeFrontBundle',
        self::OLD_ACCOUNT_GROUP => 'VitelogeFrontendBundle'
    );

    // list of routes we are testing in the rollout that are specific to the bundle we are testing
    private $routes = array(
        'viteloge_frontend_homepage',
        'viteloge_frontend_ad_search',
        'viteloge_frontend_ad_searchfromform',
        'viteloge_frontend_agency_view',

    );

    private $environment = null;

    private $dispatcher;

    public function __construct($environment,EventDispatcherInterface  $dispatcher ,array $config = array())
    {

        $this->environment = $environment;
        $this->dispatcher = $dispatcher;

        $this->enabled = isset($config['enabled']) ? $config['enabled'] : false;
        $this->testWeight = isset($config['test_weight']) ? $config['test_weight'] : 0;

    }

    /**
     * onKernelRequest
     *
     * Determine a user's test group for the new account section rollout
     * and serve the correct page.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (! $this->enabled) {
            return;
        }

        // don't need to do anything if it isn't the master request
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        // if the route isn't part of the test, we can return
        $request = $event->getRequest();
        $routeName = $request->get('_route');
        if (! in_array($routeName, $this->routes)) {
            return;
        }

        // this allows us to force an account section for testing
        // using the query parameter: ?account-section=old|new

        $forcedSection = $request->query->get('account-section');
        if ($this->environment === 'dev' && null !== $forcedSection) {
            $sectionToLoad = $forcedSection === 'old' ? self::OLD_ACCOUNT_GROUP : self::NEW_ACCOUNT_GROUP;

        }


        // determine the test group for the user whether it is already set or needs
        // to be generated. test group data is passed through a cookie
        elseif ($request->cookies->has(self::TEST_GROUP_COOKIE)) {
            $testGroup = $request->cookies->get(self::TEST_GROUP_COOKIE);
            if (! is_numeric($testGroup)) {
                $testGroup = 100;
            }

        }

        // if we haven't forced an account section, and there wasn't one found in a cookie,
        // we then randomly place the user into an account section group
        else {

            // the user's "group" is a number between 1 and 100, which allows us to easily
            // roll out to a certain percentage of people
            $testGroup = rand(1, 100);

            // since we can't set a cookie yet (no response object), we will have to temporarily
            // store the test group on the request and then wait to set the cookie in the kernel
            // response listener.
            $request->attributes->set(self::TEST_GROUP_COOKIE, $testGroup);

            // explicitly set the response event listener so that we can set the cookie later
            $this->dispatcher->addListener('kernel.response', array($this, 'onKernelResponse'), 1);

        }

        // determine the correct controller to load
        if (! isset($sectionToLoad)) {
            $cutoffPercentile = $this->testWeight * 100;
            $sectionToLoad = $testGroup <= $cutoffPercentile ? self::NEW_ACCOUNT_GROUP : self::OLD_ACCOUNT_GROUP;
        }

        // finally, route the user to the correct account controller based on their test group
        $controllerFQCN = $request->get('_controller');

        // extract what we need to build the fully qualified action name
        $pattern = '/^[\w\\\]+Bundle\\\\Controller\\\\([\w]+)Controller::([\w]+)Action$/';
        preg_match($pattern, $controllerFQCN, $matches);
        $action = sprintf("%s:%s:%s", $this->controllerGroupMapping[$sectionToLoad], $matches[1], $matches[2]);

        $request->attributes->set('_controller', $action);
    }

    /**
     * onKernelResponse
     *
     * The purpose of this function is to set the test group cookie once the response
     * object is available.  This should only ever run if there is a test group cookie
     * to set.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $testGroup = $event->getRequest()->attributes->get(self::TEST_GROUP_COOKIE);
        $testExpiration = date('Y-m-d', strtotime('+1 year'));
        $testGroupCookie = new Cookie(self::TEST_GROUP_COOKIE, $testGroup, $testExpiration);
        $event->getResponse()->headers->setCookie($testGroupCookie);
    }
}
