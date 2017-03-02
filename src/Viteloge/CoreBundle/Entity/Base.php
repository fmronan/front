<?php

namespace Viteloge\CoreBundle\Entity {

    use Doctrine\ORM\Mapping as ORM;

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


    }


}
