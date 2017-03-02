<?php

namespace Viteloge\CoreBundle\Entity {

    use Doctrine\ORM\Mapping as ORM;
    use Viteloge\CoreBundle\Entity\Ad;
    use Viteloge\CoreBundle\Entity\Infostats;


    /**
     * Contacts
     *
     * @ORM\Table(name="infos", indexes={@ORM\Index(name="date", columns={"date"})})
     * @ORM\Entity(repositoryClass="Viteloge\CoreBundle\Repository\InfosRepository")
     */
    class Infos extends Infostats
    {
        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;


        /**
         * @var string
         *
         * @ORM\Column(name="genre", type="string", length=15, nullable=false)
         */
        protected $genre;


        /**
         *
         */
        public function __construct() {
            $this->setDate(new \DateTime('now'));
        }


        /**
         *
         */
        public function initFromSearchAd(Ad $ad,$url) {

            $this->setAd($ad);
            $this->setInseeCity($ad->getInseeCity());
            $this->setAgencyId($ad->getAgencyId());
            $this->setAgencyName($ad->getAgencyName());
            $this->setAgencySpecial($ad->getAgencySpecial());
            $this->setTransaction($ad->getTransaction());
            $this->setType($ad->getType());
            $this->setRooms($ad->getRooms());
            $this->setPrice($ad->getPrice());
            $this->setCityName($ad->getCityName());
            $this->setDistrictId($ad->getDistrictId());
            $this->setPostalCode($ad->getPostalCode());
            $this->setUrl($url);

            return $this;
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
         * Set genre
         *
         * @param string $genre
         * @return Infos
         */
        public function setGenre($genre)
        {
            $this->genre = $genre;

            return $this;
        }

        /**
         * Get genre
         *
         * @return string
         */
        public function getGenre()
        {
            return $this->genre;
        }
    }


}
