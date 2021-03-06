<?php

namespace Viteloge\InseeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InseeCity
 *
 * @ORM\Table(name="insee_communes", indexes={@ORM\Index(name="postalcode", columns={"cp"}), @ORM\Index(name="lat", columns={"lat"}), @ORM\Index(name="lng", columns={"lon"}), @ORM\Index(name="geolevel", columns={"niveauGeo"}), @ORM\Index(name="isCapital", columns={"chefLieu"}), @ORM\Index(name="stateId", columns={"codeRegion"}), @ORM\Index(name="departmentId", columns={"codeDepartement"}), @ORM\Index(name="code", columns={"codeCommune"}), @ORM\Index(name="distictId", columns={"codeArrondissment"}), @ORM\Index(name="cantonId", columns={"codeCanton"}), @ORM\Index(name="population", columns={"population"}), @ORM\Index(name="basin", columns={"bassinVie"}), @ORM\Index(name="uname", columns={"search_nom"}), @ORM\Index(name="name", columns={"nom"})})
 * @ORM\Entity(repositoryClass="Viteloge\InseeBundle\Repository\InseeCityRepository")
 */
class InseeCity extends InseeEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="niveauGeo", type="string", length=3, nullable=false)
     */
    protected $geolevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="decoupageCanton", type="smallint", nullable=false)
     */
    protected $isCantonDivided;

    /**
     * @var integer
     *
     * @ORM\Column(name="chefLieu", type="smallint", nullable=false)
     */
    protected $isCapital;

    /**
     * @var string
     *
     * @ORM\Column(name="codeCommune", type="string", length=3, nullable=false)
     */
    protected $code;

    /**
     * @var boolean
     *
     * @ORM\Column(name="codeArrondissment", type="boolean", nullable=false)
     */
    protected $districtId;

    /**
     * @var string
     *
     * @ORM\Column(name="codeCanton", type="string", length=2, nullable=false)
     */
    protected $cantonId;

    /**
     * @var integer
     *
     * @ORM\Column(name="typeNomClair", type="smallint", nullable=false)
     */
    protected $prefixId;

    /**
     * @var string
     *
     * @ORM\Column(name="article", type="string", length=10, nullable=false)
     */
    protected $prefix;

    /**
     * @var string
     *
     * @ORM\Column(name="search_nom", type="text", nullable=false)
     */
    protected $uname;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="text", nullable=false)
     */
    protected $name;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float", precision=10, scale=0, nullable=false)
     */
    protected $lat;

    /**
     * @var float
     *
     * @ORM\Column(name="lon", type="float", precision=10, scale=0, nullable=false)
     */
    protected $lng;

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=8, nullable=false)
     */
    protected $postalcode;

    /**
     * @var string
     *
     * @ORM\Column(name="bassinVie", type="string", length=5, nullable=false)
     */
    protected $basin;

    /**
     * @var integer
     *
     * @ORM\Column(name="population", type="integer", nullable=false)
     */
    protected $population;

    /**
     * @var string
     *
     * @ORM\Column(name="codeInsee", type="string", length=5)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Viteloge\InseeBundle\Entity\InseeState
     *
     * @ORM\ManyToOne(targetEntity="Viteloge\InseeBundle\Entity\InseeState")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="codeRegion", referencedColumnName="REGION", nullable=true)
     * })
     */
    protected $inseeState;

    /**
     * @var \Viteloge\InseeBundle\Entity\InseeDepartment
     *
     * @ORM\ManyToOne(targetEntity="Viteloge\InseeBundle\Entity\InseeDepartment", inversedBy="inseeCities")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="codeDepartement", referencedColumnName="DEP", nullable=true)
     * })
     */
    protected $inseeDepartment;

    /**
     *
     */
    public function __construct() {

    }

    /**
     * Set geolevel
     *
     * @param string $geolevel
     * @return InseeCity
     */
    public function setGeolevel($geolevel)
    {
        $this->geolevel = $geolevel;

        return $this;
    }

    /**
     * Get geolevel
     *
     * @return string
     */
    public function getGeolevel()
    {
        return $this->geolevel;
    }

    /**
     * Set isCantonDivided
     *
     * @param integer $isCantonDivided
     * @return InseeCity
     */
    public function setCantonDivided($isCantonDivided)
    {
        $this->isCantonDivided = $isCantonDivided;

        return $this;
    }

    /**
     * Get isCantonDivided
     *
     * @return integer
     */
    public function getCantonDivided()
    {
        return $this->isCantonDivided;
    }

    /**
     * Set isCapital
     *
     * @param integer $isCapital
     * @return InseeCity
     */
    public function setCapital($isCapital)
    {
        $this->isCapital = $isCapital;

        return $this;
    }

    /**
     * Get isCapital
     *
     * @return integer
     */
    public function getCapital()
    {
        return $this->isCapital;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return InseeCity
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set districtId
     *
     * @param boolean $districtId
     * @return InseeCity
     */
    public function setDistrictId($districtId)
    {
        $this->districtId = $districtId;

        return $this;
    }

    /**
     * Get districtId
     *
     * @return boolean
     */
    public function getDistrictId()
    {
        return $this->districtId;
    }

    /**
     * Set cantonId
     *
     * @param string $cantonId
     * @return InseeCity
     */
    public function setCantonId($cantonId)
    {
        $this->cantonId = $cantonId;

        return $this;
    }

    /**
     * Get cantonId
     *
     * @return string
     */
    public function getCantonId()
    {
        return $this->cantonId;
    }

    /**
     * Set prefixId
     *
     * @param integer $prefixId
     * @return InseeCity
     */
    public function setPrefixId($prefixId)
    {
        $this->prefixId = $prefixId;

        return $this;
    }

    /**
     * Get prefixId
     *
     * @return integer
     */
    public function getPrefixId()
    {
        return $this->prefixId;
    }

    /**
     * Set prefix
     *
     * @param string $prefix
     * @return InseeCity
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set uname
     *
     * @param string $uname
     * @return InseeCity
     */
    public function setUname($uname)
    {
        $this->uname = $uname;

        return $this;
    }

    /**
     * Get uname
     *
     * @return string
     */
    public function getUname()
    {
        return $this->uname;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return InseeCity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lat
     *
     * @param float $lat
     * @return InseeCity
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param float $lng
     * @return InseeCity
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Return the location
     * @return string
     */
    public function getLocation() {
        $location = $this->getLat().','.$this->getLng();
        return $location;
    }

    /**
     * Set postalcode
     *
     * @param string $postalcode
     * @return InseeCity
     */
    public function setPostalcode($postalcode)
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    /**
     * Get postalcode
     *
     * @return string
     */
    public function getPostalcode()
    {
        return $this->postalcode;
    }

    /**
     * Set basin
     *
     * @param string $basin
     * @return InseeCity
     */
    public function setBasin($basin)
    {
        $this->basin = $basin;

        return $this;
    }

    /**
     * Get basin
     *
     * @return string
     */
    public function getBasin()
    {
        return $this->basin;
    }

    /**
     * Set population
     *
     * @param integer $population
     * @return InseeCity
     */
    public function setPopulation($population)
    {
        $this->population = $population;

        return $this;
    }

    /**
     * Get population
     *
     * @return integer
     */
    public function getPopulation()
    {
        return $this->population;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set inseeState
     *
     * @param \Viteloge\InseeBundle\Entity\InseeState $inseeState
     * @return InseeCity
     */
    public function setInseeState(\Viteloge\InseeBundle\Entity\InseeState $inseeState = null)
    {
        $this->inseeState = $inseeState;

        return $this;
    }

    /**
     * Get inseeState
     *
     * @return \Viteloge\InseeBundle\Entity\InseeState
     */
    public function getInseeState()
    {
        return $this->inseeState;
    }

    /**
     * Set inseeDepartment
     *
     * @param \Viteloge\InseeBundle\Entity\InseeDepartment $inseeDepartment
     * @return InseeCity
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

    /**
     *
     * @return string
     */
    public function getNameAndPostalcode() {
        return $this->getName().' ('.$this->getPostalcode().')';
    }

}
