<?php

namespace Viteloge\UserBundle\Services {


    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Cookie;


    class AbUserFactory {

        private $requestStack;
        private $request;
       // enables the rollout test
        private $enabled;

        // percentage of people (in the test locales) who see the new account section at any given time
        // a value of .1 would mean 10% of people (in the test locales) see new account section
        private $testWeight;

         public function __construct(RequestStack $requestStack,array $config = array()){

            $this->requestStack = $requestStack;
            $this->request = $this->requestStack->getCurrentRequest();
            $this->enabled = isset($config['enabled']) ? $config['enabled'] : false;
            $this->testWeight = isset($config['test_weight']) ? $config['test_weight'] : 0;

         }

        //function de verification pour ab testing pour le UserBundle

        public function abVerif(){
            $sectionToLoad = 'UserBundle';

            if (! $this->enabled) {
            return $sectionToLoad;
            }
            $cookies = $this->request->cookies;
            if($cookies->has('account_rollout_group')){
                $testGroup = $cookies->get('account_rollout_group');
                $cutoffPercentile = $this->testWeight * 100;
                $sectionToLoad = $testGroup <= $cutoffPercentile ? 'UsersBundle' : 'UserBundle';
            }else{

            }

            return $sectionToLoad;

        }

    }

}
