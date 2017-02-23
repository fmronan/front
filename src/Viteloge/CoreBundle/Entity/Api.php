<?php

namespace Viteloge\CoreBundle\Entity {

    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;
    use Viteloge\InseeBundle\Entity\InseeCity;

    /**
     * @ORM\Entity
     */
    class Api {

        /**
         * @var integer
         *
         * @ORM\Id
         * @ORM\Column(name="id", type="integer")
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var \Viteloge\InseeBundle\Entity\InseeCity
         *
         * @Assert\Type(type="Viteloge\InseeBundle\Entity\InseeCity")
         * @Assert\Valid()
         * @Assert\NotBlank()
         */
        protected $inseeCity;

        /**
         * Constructor
         */
        public function __construct() {
            $this->inseeCity = null;
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
         * Get inseeCity
         *
         * @return \Viteloge\InseeBundle\Entity\InseeCity
         */
        public function getInseeCity() {
            return $this->inseeCity;
        }

        /**
         * Set inseeCity
         *
         * @param \Viteloge\InseeBundle\Entity\InseeCity $inseeCity
         * @return Api
         */
        public function setInseeCity(\Viteloge\InseeBundle\Entity\InseeCity $inseeCity) {
            $this->inseeCity = $inseeCity;

            return $this;
        }

    }

}
