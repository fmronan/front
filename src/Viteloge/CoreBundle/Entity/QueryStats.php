<?php

namespace Viteloge\CoreBundle\Entity {

    use Doctrine\ORM\Mapping as ORM;


    /**
     * QueryStats
     *
     * @ORM\Table(name="query_stats", uniqueConstraints={@ORM\UniqueConstraint(name="md5", columns={"md5"})}, indexes={@ORM\Index(name="keywords", columns={"keywords"}), @ORM\Index(name="transaction", columns={"transaction"}), @ORM\Index(name="type", columns={"type"}), @ORM\Index(name="rooms", columns={"pieces"}), @ORM\Index(name="cityId", columns={"insee"}), @ORM\Index(name="dpt", columns={"dpt"}), @ORM\Index(name="stateId", columns={"region"}), @ORM\Index(name="count", columns={"count"}), @ORM\Index(name="urlrewrite", columns={"urlrewrite"})})
     * @ORM\Entity(repositoryClass="Viteloge\CoreBundle\Repository\QueryStatsRepository")
     */
    class QueryStats
    {
        /**
         * @var string
         *
         * @ORM\Column(name="md5", type="string", length=255, nullable=false)
         */
        protected $md5;

        /**
         * @var string
         *
         * @ORM\Column(name="keywords", type="string", length=255, nullable=false)
         */
        protected $keywords;

        /**
         * @var string
         *
         * @ORM\Column(name="urlrewrite", type="string", length=255, nullable=false)
         */
        protected $urlrewrite;

        /**
         * @var string
         *
         * @ORM\Column(name="transaction", type="string", length=1, nullable=true)
         */
        protected $transaction;

        /**
         * @var string
         *
         * @ORM\Column(name="type", type="string", length=50, nullable=true)
         */
        protected $type;

        /**
         * @var integer
         *
         * @ORM\Column(name="pieces", type="integer", nullable=true)
         */
        protected $rooms;

        /**
         * @var integer
         *
         * @ORM\Column(name="region", type="smallint", nullable=true)
         */
        protected $stateId;

        /**
         * @var integer
         *
         * @ORM\Column(name="count", type="integer", nullable=false)
         */
        protected $count;

        /**
         * @var integer
         *
         * @ORM\Column(name="timestamp", type="integer", nullable=false)
         */
        protected $timestamp;

        /**
         * @var string
         *
         * @ORM\Column(name="real_search", type="string", length=255, nullable=true)
         */
        protected $realSearch;

        /**
         * @var integer
         *
         * @ORM\Column(name="idQuery", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        protected $id;

        /**
         * @var \Viteloge\InseeBundle\Entity\InseeCity
         *
         * @ORM\ManyToOne(targetEntity="Viteloge\InseeBundle\Entity\InseeCity")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="insee", referencedColumnName="codeInsee")
         * })
         */
        protected $inseeCity;

        /**
         * @var \Viteloge\InseeBundle\Entity\InseeDepartment
         *
         * @ORM\ManyToOne(targetEntity="Viteloge\InseeBundle\Entity\InseeDepartment")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="dpt", referencedColumnName="DEP")
         * })
         */
        protected $inseeDepartment;



        /**
         * Set md5
         *
         * @param string $md5
         * @return QueryStats
         */
        public function setMd5($md5)
        {
            $this->md5 = $md5;

            return $this;
        }

        /**
         * Get md5
         *
         * @return string
         */
        public function getMd5()
        {
            return $this->md5;
        }

        /**
         * Set keywords
         *
         * @param string $keywords
         * @return QueryStats
         */
        public function setKeywords($keywords)
        {
            $this->keywords = $keywords;

            return $this;
        }

        /**
         * Get keywords
         *
         * @return string
         */
        public function getKeywords()
        {
            return $this->keywords;
        }

        /**
         * Set urlrewrite
         *
         * @param string $urlrewrite
         * @return QueryStats
         */
        public function setUrlrewrite($urlrewrite)
        {
            $this->urlrewrite = $urlrewrite;

            return $this;
        }

        /**
         * Get urlrewrite
         *
         * @return string
         */
        public function getUrlrewrite() {
            return $this->urlrewrite;
        }

        /**
         * getUrlrewrite alias
         *
         * @return string
         */
        public function getSlug() {
            return $this->urlrewrite;
        }

        /**
         * Set transaction
         *
         * @param string $transaction
         * @return QueryStats
         */
        public function setTransaction($transaction)
        {
            $this->transaction = $transaction;

            return $this;
        }

        /**
         * Get transaction
         *
         * @return string
         */
        public function getTransaction()
        {
            return $this->transaction;
        }

        /**
         * Set type
         *
         * @param string $type
         * @return QueryStats
         */
        public function setType($type)
        {
            $this->type = $type;

            return $this;
        }

        /**
         * Get type
         *
         * @return string
         */
        public function getType()
        {
            return $this->type;
        }

        /**
         * Set rooms
         *
         * @param integer $rooms
         * @return QueryStats
         */
        public function setRooms($rooms)
        {
            $this->rooms = $rooms;

            return $this;
        }

        /**
         * Get rooms
         *
         * @return integer
         */
        public function getRooms()
        {
            return $this->rooms;
        }

        /**
         * Set stateId
         *
         * @param integer $stateId
         * @return QueryStats
         */
        public function setStateId($stateId)
        {
            $this->stateId = $stateId;

            return $this;
        }

        /**
         * Get stateId
         *
         * @return integer
         */
        public function getStateId()
        {
            return $this->stateId;
        }

        /**
         * Set count
         *
         * @param integer $count
         * @return QueryStats
         */
        public function setCount($count)
        {
            $this->count = $count;

            return $this;
        }

        /**
         * Get count
         *
         * @return integer
         */
        public function getCount()
        {
            return $this->count;
        }

        /**
         * Set timestamp
         *
         * @param integer $timestamp
         * @return QueryStats
         */
        public function setTimestamp($timestamp)
        {
            $this->timestamp = $timestamp;

            return $this;
        }

        /**
         * Get timestamp
         *
         * @return integer
         */
        public function getTimestamp()
        {
            return $this->timestamp;
        }

        /**
         * Return a \DateTime from internal timestamp. use for cache policies
         *
         * @return \DateTime
         */
        public function getUpdateAt() {
            $ts = $this->getTimestamp();
            return new \DateTime("@$ts");
        }

        /**
         * Set realSearch
         *
         * @param string $realSearch
         * @return QueryStats
         */
        public function setRealSearch($realSearch)
        {
            $this->realSearch = $realSearch;

            return $this;
        }

        /**
         * Get realSearch
         *
         * @return string
         */
        public function getRealSearch()
        {
            return $this->realSearch;
        }

        /**
         * Get id
         *
         * @return integer
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set inseeCity
         *
         * @param \Viteloge\InseeBundle\Entity\InseeCity $inseeCity
         * @return QueryStats
         */
        public function setInseeCity(\Viteloge\InseeBundle\Entity\InseeCity $inseeCity = null)
        {
            $this->inseeCity = $inseeCity;

            return $this;
        }

        /**
         * Get inseeCity
         *
         * @return \Viteloge\InseeBundle\Entity\InseeCity
         */
        public function getInseeCity()
        {
            return $this->inseeCity;
        }

        /**
         * Set inseeDepartment
         *
         * @param \Viteloge\InseeBundle\Entity\InseeDepartment $inseeDepartment
         * @return QueryStats
         */
        public function setInseeDepartment(\Viteloge\InseeBundle\Entity\InseeDepartment $inseeDepartment = null)
        {
            $this->inseeDepartment = $inseeDepartment;

            return $this;
        }

        /**
         * Get inseeDepartment
         *
         * @return \Viteloge\InseeBundle\Entity\InseeDepartment
         */
        public function getInseeDepartment()
        {
            return $this->inseeDepartment;
        }
    }


}
