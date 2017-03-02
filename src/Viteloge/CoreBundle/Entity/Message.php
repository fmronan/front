<?php

namespace Viteloge\CoreBundle\Entity {

    use Doctrine\ORM\Mapping as ORM;
    use Viteloge\CoreBundle\Entity\Ad;
    use Viteloge\CoreBundle\Entity\Messagerie;

    /**
     * Message
     *
     * @ORM\Table(name="message")
     * @ORM\Entity(repositoryClass="Viteloge\CoreBundle\Repository\MessageRepository")
     */
    class Message extends Messagerie{

        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         *
         * @ORM\ManyToOne(targetEntity="Viteloge\CoreBundle\Entity\Ad")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="idAnnonce", referencedColumnName="idAnnonce")
         * })
         */
        protected $ad;


        /**
         *
         */
        public function __construct(Ad $ad) {
            $this->setAd($ad);
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


        /**
         *
         */
        public function getAd() {
            return $this->ad;
        }

        /**
         *
         */
        public function setAd(Ad $ad) {
            $this->ad = $ad;
            return $this;
        }


    }

}
