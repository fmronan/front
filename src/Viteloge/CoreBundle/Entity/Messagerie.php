<?php

namespace Viteloge\CoreBundle\Entity {

    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;
    use Viteloge\CoreBundle\Entity\Base;


    class Messagerie extends Base{

        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @Assert\Regex(
         *     pattern="/\d/",
         *     match=true,
         *     message="viteloge.assert.phone"
         * )
         * @ORM\Column(name="phone",type="string",length=15, nullable=true)
         */
        protected $phone;


        /**
         * @return Contacts
         */
        protected function updateCreatedAt() {
            $this->date->setDate($this->getYear(), $this->getMonth(), $this->getDay());
            return $this;
        }

        /**
         *
         */
        public function getPhone() {
            return $this->phone;
        }

        /**
         *
         */
        public function setPhone($phone) {
            $this->phone = $phone;
            return $this;
        }
    }

}
