<?php

namespace Viteloge\CoreBundle\Entity {

    use Doctrine\ORM\Mapping as ORM;
    use Viteloge\CoreBundle\Entity\Infostats;

    /**
     * Statistics
     *
     * @ORM\Table(name="statistiques", indexes={@ORM\Index(name="date", columns={"date"})})
     * @ORM\Entity(repositoryClass="Viteloge\CoreBundle\Repository\StatisticsRepository")
     */
    class Statistics extends Infostats
    {
        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        protected $id;


        /**
         *
         */
        public function __construct() {
            $this->setDate(new \DateTime('now'));
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


    }


}
