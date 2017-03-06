<?php

namespace Viteloge\CoreBundle\Entity {

    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;
    use Viteloge\CoreBundle\Entity\User as CoreUser;

    class Base
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
         * @var \DateTime
         *
         * @ORM\Column(name="date", type="datetime", nullable=false)
         */
        protected $date;

        /**
         * @var integer
         *
         * @ORM\Column(name="annee", type="smallint", nullable=false)
         */
        protected $year;

        /**
         * @var integer
         *
         * @ORM\Column(name="mois", type="smallint", nullable=false)
         */
        protected $month;

        /**
         * @var integer
         *
         * @ORM\Column(name="jour", type="smallint", nullable=false)
         */
        protected $day;

        /**
         * @var string
         *
         * @ORM\Column(name="ip", type="string", length=15, nullable=false)
         */
        protected $ip;

        /**
         * @var string
         *
         * @ORM\Column(name="UA", type="string", length=128, nullable=false)
         */
        protected $ua;

        /**
         *
         */
        protected $user;

        /**
         * @Assert\NotBlank()
         * @Assert\Length(
         *      min = "2",
         *      max = "64"
         * )
         */
        protected $firstname;

        /**
         * @Assert\NotBlank()
         * @Assert\Length(
         *      min = "2",
         *      max = "64"
         * )
         */
        protected $lastname;

        /**
         * @Assert\NotBlank()
         * @Assert\Email(
         *      checkHost = true,
         *      checkMX = true
         * )
         */
        protected $email;

        /**
         * @Assert\NotBlank()
         * @Assert\Length(
         *      min = "5",
         *      max = "250"
         * )
         */
        protected $message;

        /**
         * @Assert\Count(
         *      min = "1",
         *      max = "10",
         *      minMessage = "assert.count.recommand.validate.min",
         *      maxMessage = "assert.count.recommand.validate.max"
         * )
         * @Assert\All(
         *     @Assert\Email()
         * )
         */
        protected $emails;

        /**
         * Get date
         *
         * @return \DateTime
         */
        public function getDate()
        {
            return $this->date;
        }

        /**
         * Set date
         *
         * @param \DateTime $date
         * @return Contact
         */
        public function setDate(\DateTime $date) {
            try {
                $this->date = clone $date;
                $this->setYear((int)$date->format('Y'));
                $this->setMonth((int)$date->format('m'));
                $this->setDay((int)$date->format('d'));
            } catch (\Exception $e) {

            }
            return $this;
         }

        /**
         * Set year
         *
         * @param integer $year
         * @return Statistics
         */
        public function setYear($year) {
            if (is_int($year)) {
                $this->year = $year;
                $this->updateCreatedAt();
            }

            return $this;
        }

        /**
         * Get year
         *
         * @return integer
         */
        public function getYear()
        {
            return $this->year;
        }

        /**
         * Set month
         *
         * @param integer $month
         * @return Statistics
         */
        public function setMonth($month) {
            if (is_int($month)) {
                $this->month = $month;
                $this->updateCreatedAt();
            }

            return $this;
        }

        /**
         * Get month
         *
         * @return integer
         */
        public function getMonth()
        {
            return $this->month;
        }

        /**
         * Set day
         *
         * @param integer $day
         * @return Statistics
         */
        public function setDay($day) {
            if (is_int($day)) {
                $this->day = $day;
                $this->updateCreatedAt();
            }

            return $this;
        }

        /**
         * Get day
         *
         * @return integer
         */
        public function getDay()
        {
            return $this->day;
        }

        /**
         * Set ip
         *
         * @param string $ip
         * @return Statistics
         */
        public function setIp($ip)
        {
            $this->ip = $ip;

            return $this;
        }

        /**
         * Get ip
         *
         * @return string
         */
        public function getIp()
        {
            return $this->ip;
        }

        /**
         * Set ua
         *
         * @param string $ua
         * @return Statistics
         */
        public function setUa($ua)
        {
            $this->ua = $ua;

            return $this;
        }

        /**
         * Get ua
         *
         * @return string
         */
        public function getUa()
        {
            return $this->ua;
        }

        /**
         *
         */
        public function getUser() {
            return $this->user;
        }

        /**
         *
         */
        public function setUser(CoreUser $user = null) {
            if ($user instanceof CoreUser) {
                $this->email = $this->user->getEmail();
                $this->lastname = $this->user->getLastname();
                $this->firstname = $this->user->getFirstname();
            }
            return $this;
        }

        /**
         *
         */
        public function getFirstname() {
            return $this->firstname;
        }

        /**
         *
         */
        public function setFirstname($firstname) {
            $this->firstname = $firstname;
            return $this;
        }

        /**
         *
         */
        public function getLastname() {
            return $this->lastname;
        }

        /**
         *
         */
        public function setLastname($lastname) {
            $this->lastname = $lastname;
            return $this;
        }

        /**
         *
         */
        public function getFullname() {
            return trim($this->getFirstname().' '.$this->getLastname());
        }

        /**
         *
         */
        public function getEmail() {
            return $this->email;
        }

        /**
         *
         */
        public function setEmail($email) {
            $this->email = $email;
            return $this;
        }

        /**
         *
         */
        public function getMessage() {
            return $this->message;
        }

        /**
         *
         */
        public function setMessage($message) {
            $this->message = $message;
            return $this;
        }

        /**
         * Add email
         *
         */
        public function addEmail($email) {
            $this->emails[] = $email;

            return $this;
        }

        /**
         * Remove email
         */
        public function removeEmail($email) {
            $this->emails->removeElement($email);
        }

        /**
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getEmails() {
            return $this->emails;
        }

    }


}
