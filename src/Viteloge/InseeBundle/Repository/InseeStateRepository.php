<?php

namespace Viteloge\InseeBundle\Repository {

    use Doctrine\ORM\EntityRepository;
    use Doctrine\ORM\NoResultException;

    /**
     * InseeStateRepository
     *
     * This class was generated by the Doctrine ORM. Add your own custom
     * repository methods below.
     */
    class InseeStateRepository extends EntityRepository {

        /**
         *
         */
        public function findOneBySoundex($soundex) {
            $query = $this->getEntityManager()
                ->createQuery(
                    'SELECT i '.
                    'FROM AcreatInseeBundle:InseeState i '.
                    'WHERE SOUNDEX(i.name) = SOUNDEX(:soundex) '
                )
                ->setParameter(':soundex', $soundex)
                ->setMaxResults(1);
            try {
                return $query->getSingleResult();
            } catch (NoResultException $e) {
                return null;
            }
        }

    }

}
