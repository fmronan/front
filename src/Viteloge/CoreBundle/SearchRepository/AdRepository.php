<?php

namespace Viteloge\CoreBundle\SearchRepository {

    use FOS\ElasticaBundle\Repository as EntityRepository;
    use Viteloge\CoreBundle\SearchEntity\Ad;
    use Viteloge\InseeBundle\Entity\InseeArea;
    use Symfony\Component\DependencyInjection\ContainerAwareInterface;
    use Symfony\Component\DependencyInjection\ContainerAwareTrait;

    /**
     * AdRepository
     *
     * This class was generated by the Doctrine ORM. Add your own custom
     * repository methods below.
     */
    class AdRepository extends EntityRepository implements ContainerAwareInterface{
     use ContainerAwareTrait;
        /**
         *
         */
        public function getQueryForSearch(Ad $ad) {
            $boolQuery = new \Elastica\Filter\BoolFilter();
            $fieldQuery = new \Elastica\Query\MatchAll();
            $transaction = $ad->getTransaction();
            $adTransaction = strtolower($transaction[0]);
            if (!empty($adTransaction)) {
                $transactionTermQuery = new \Elastica\Filter\Terms();
                $transactionTermQuery->setTerms('transaction', array($adTransaction));
                $boolQuery->addMust($transactionTermQuery);
            }

            $adWhereArea = $ad->getWhereArea();
            if (!empty($adWhereArea)) {
               $terms = $this->container->get('viteloge_core.search.insee')->getArea($ad);
                if (!empty($terms)) {
                    $areaTermQuery = new \Elastica\Filter\Terms();
                    $areaTermQuery->setTerms('description', $terms);
                    $boolQuery->addMust($areaTermQuery);
                }
            }

            $adRadius = $ad->getRadius();
            $adLocation = $ad->getLocation();
            $adWhere = $ad->getWhere();
            if (!empty($adRadius) && !empty($adLocation)) {
                $radiusDistanceQuery = new \Elastica\Filter\GeoDistance('location', $adLocation, $adRadius.'km');
                $radiusQuery = new \Elastica\Filter\BoolFilter();
                $radiusQuery->addMust($radiusDistanceQuery);
                $cityHasParent = new \Elastica\Filter\HasParent($radiusQuery, 'inseeCity');
                $boolQuery->addMust($cityHasParent);
            }
            elseif (!empty($adWhere)) {
                $cityTermsQuery = new \Elastica\Filter\Terms();
                $cityTermsQuery->setTerms('id', $adWhere);
                $cityQuery = new \Elastica\Filter\BoolFilter();
                $cityQuery->addMust($cityTermsQuery);
                $cityHasParent = new \Elastica\Filter\HasParent($cityQuery, 'inseeCity');
                $boolQuery->addMust($cityHasParent);
            }

            $adWhereDepartment = $ad->getWhereDepartment();
            if (!empty($adWhereDepartment)) {
                $departmentTermsQuery = new \Elastica\Filter\Terms();
                $departmentTermsQuery->setTerms('inseeDepartment.department_id', $adWhereDepartment);
                $departmentBoolQuery = new \Elastica\Filter\BoolFilter();
                $departmentBoolQuery->addMust($departmentTermsQuery);
                $departmentQuery = new \Elastica\Filter\Nested();
                $departmentQuery->setPath('inseeDepartment');
                $departmentQuery->setFilter($departmentBoolQuery);
                $cityHasParent = new \Elastica\Filter\HasParent($departmentQuery, 'inseeCity');
                $boolQuery->addMust($cityHasParent);
            }

            $adWhereState = $ad->getWhereState();
            if (!empty($adWhereState)) {
                $stateTermsQuery = new \Elastica\Filter\Terms();
                $stateTermsQuery->setTerms('inseeState.state_id', $adWhereState);
                $stateBoolQuery = new \Elastica\Filter\BoolFilter();
                $stateBoolQuery->addMust($stateTermsQuery);
                $stateQuery = new \Elastica\Filter\Nested();
                $stateQuery->setPath('inseeState');
                $stateQuery->setFilter($stateBoolQuery);
                $cityHasParent = new \Elastica\Filter\HasParent($stateQuery, 'inseeCity');
                $boolQuery->addMust($cityHasParent);
            }

            $adWhat = $ad->getWhat();
            if (!empty($adWhat)) {
                $whatQuery = new \Elastica\Filter\Terms();
                $whatQuery->setTerms('type', $adWhat);
                $boolQuery->addMust($whatQuery);
            }

            $adRooms = $ad->getRooms();
            if (!empty($adRooms)) {
                $roomQuery = new \Elastica\Filter\Terms();
                $roomQuery->setTerms('rooms', $adRooms);
                if (in_array(5, $adRooms)) {
                    $rangeQuery = new \Elastica\Filter\Range(
                        'rooms', array(
                            'gt' => 5
                        )
                    );
                    $orQuery = new \Elastica\Filter\BoolOr();
                    $orQuery->addFilter($roomQuery);
                    $orQuery->addFilter($rangeQuery);
                    $boolQuery->addMust($orQuery);
                } else {
                    $boolQuery->addMust($roomQuery);
                }
            }

            $adMinPrice = $ad->getMinPrice();
            $adMaxPrice = $ad->getMaxPrice();
            if (!empty($adMinPrice) && !empty($adMaxPrice)) {
                $boolQuery->addMust(
                    new \Elastica\Filter\Range(
                        'price',
                        array(
                            'gte' => $adMinPrice,
                            'lte' => $adMaxPrice
                        )
                    )
                );
            }
            elseif(!empty($adMinPrice)) {
                $boolQuery->addMust(
                    new \Elastica\Filter\Range(
                        'price',
                        array(
                            'gte' => $adMinPrice
                        )
                    )
                );
            }
            elseif(!empty($adMaxPrice)) {
                $boolQuery->addMust(
                    new \Elastica\Filter\Range(
                        'price',
                        array(
                            'lte' => $adMaxPrice
                        )
                    )
                );
            }

            $sort = array();
            $adSort = $ad->getSort();
            if (!empty($adSort) && $adSort != 'default') {
                $sort[$ad->getSort()] = array( 'order' => $ad->getDirection() );
            }
            $sort['privilegeRank'] = array( 'order' => 'desc' );
            $sort['order'] = array( 'order' => 'desc' );

            $filtered = new \Elastica\Query\Filtered();
            $filtered->setQuery($fieldQuery);
            $filtered->setFilter($boolQuery);

            $query = new \Elastica\Query($filtered);
            $query->setSort($sort);

            return $query;
        }

        /**
         *
         */
        public function search(Ad $ad, $limit=1000000) {
            $query = $this->getQueryForSearch($ad);
            return $this->find($query, $limit);
        }

        /**
         *
         */
        public function searchPaginated(Ad $ad) {
            $query = $this->getQueryForSearch($ad);
            return $this->findPaginated($query);
        }

    }


}
