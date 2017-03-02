<?php

namespace Viteloge\CoreBundle\Entity {

    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;
    use Viteloge\CoreBundle\Entity\Messagerie;

    /**
     * Contact
     *
     * @ORM\Table(name="contact")
     * @ORM\Entity(repositoryClass="Viteloge\CoreBundle\Repository\ContactRepository")
     */
    class Contact extends Messagerie{

        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @Assert\Length(
         *      min = "2",
         *      max = "64"
         * )
         * @ORM\Column(name="company",type="string",length=255, nullable=true)
         */
        protected $company;

        /**
         * @Assert\NotBlank()
         * @Assert\Choice(
         *      callback = {"Viteloge\CoreBundle\Component\Enum\SubjectEnum", "getValues"},
         *      multiple = false,
         * )
         * @ORM\Column(name="subject",type="string",length=255)
         */
        protected $subject;

        /**
         * @Assert\Length(
         *      min = "5",
         *      max = "250"
         * )
         * @ORM\Column(name="address",type="string",length=255, nullable=true)
         */
        protected $address;

        /**
         * @Assert\Regex(
         *     pattern="/\d/",
         *     match=true,
         *     message="viteloge.assert.phone"
         * )
         * @ORM\Column(name="postalCode",type="string",length=5, nullable=true)
         */
        protected $postalCode;

        /**
         * @Assert\NotBlank()
         * @Assert\Length(
         *      min = "2",
         *      max = "64"
         * )
         * @ORM\Column(name="city",type="string",length=50, nullable=false)
         */
        protected $city;

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



        /**
         *
         */
        public function getCompany() {
            return $this->company;
        }

        /**
         *
         */
        public function setCompany($company) {
            $this->company = $company;
            return $this;
        }


        /**
         *
         */
        public function getSubject() {
            return $this->subject;
        }

        /**
         *
         */
        public function setSubject($subject) {
            $this->subject = $subject;
            return $this;
        }

        /**
         *
         */
        public function getAddress() {
            return $this->message;
        }

        /**
         *
         */
        public function setAddress($address) {
            $this->address = $address;
            return $this;
        }

        /**
         *
         */
        public function getPostalCode() {
            return $this->postalCode;
        }

        /**
         *
         */
        public function setPostalCode($postalCode) {
            $this->postalCode = $postalCode;
            return $this;
        }

        /**
         *
         */
        public function getCity() {
            return $this->city;
        }

        /**
         *
         */
        public function setCity($city) {
            $this->city = $city;
            return $this;
        }
    }

}
