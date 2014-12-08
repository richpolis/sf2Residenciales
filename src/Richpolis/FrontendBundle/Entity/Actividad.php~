<?php

namespace Richpolis\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Actividades
 *
 * @ORM\Table(name="actividades")
 * @ORM\Entity(repositoryClass="Richpolis\FrontendBundle\Repository\ActividadesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Actividad
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     * @Assert\NotBlank(message="Ingresa un nombre a la actividad")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_actividad", type="date")
     */
    private $fechaActividad;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="desde", type="time")
     */
    private $desde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hasta", type="time")
     */
    private $hasta;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_acceso", type="integer")
     */
    private $tipoAcceso;
    
    /**
     * @var \Residencial
     * @todo Administrador de la residencial
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\BackendBundle\Entity\Residencial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="residencial_id", referencedColumnName="id")
     * })
     */
    private $residencial;
    
    /**
     * @var integer
     * @todo Edificios dentro de la residencial asociados. 
     *
     * @ORM\ManyToMany(targetEntity="Richpolis\BackendBundle\Entity\Edificio", inversedBy="actividades")
     * @ORM\JoinTable(name="actividades_edificios")
     * @ORM\OrderBy({"nombre" = "ASC"})
     */
    private $edificios;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="date")
     */
    private $createdAt;
    
    /*
     * Timestable
     */
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(!$this->getCreatedAt())
        {
          $this->createdAt = new \DateTime();
        }
    }
    
    const TIPO_ACCESO_RESIDENCIAL=1;
    const TIPO_ACCESO_EDIFICIO=2;
    const TIPO_ACCESO_PRIVADO=3;
        
    static public $sTipoAcceso=array(
        self::TIPO_ACCESO_RESIDENCIAL=>'Residencial',
        self::TIPO_ACCESO_EDIFICIO=>'Torre',
        self::TIPO_ACCESO_PRIVADO=>'Privado',
    );
    
    public function getStringTipoAcceso(){
        return self::$sTipoAcceso[$this->getTipoAcceso()];
    }
    static function getArrayTipoAcceso(){
        return self::$sTipoAcceso;
    }
    static function getPreferedTipoAcceso(){
        return array(self::TIPO_ACCESO_RESIDENCIAL);
    }
    
    public function diaSemana($dia){
        switch($dia){
            case 0: return "Dom";
            case 1: return "Lun";
            case 2: return "Mar";
            case 3: return "Mie";
            case 4: return "Jue";
            case 5: return "Vie";
            case 6: return "Sab";
        }
    }
    
    public function nombreMes($mes){
        switch($mes){
            case 1: return "Ene";
            case 2: return "Feb";
            case 3: return "Mar";
            case 4: return "Abr";
            case 5: return "May";
            case 6: return "Jun";
            case 7: return "Jul";
            case 8: return "Ago";
            case 9: return "Sep";
            case 10: return "Oct";
            case 11: return "Nov";
            case 12: return "Dic";
        }
    }

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->edificios = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nombre
     *
     * @param string $nombre
     * @return Actividad
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Actividad
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set fechaActividad
     *
     * @param \DateTime $fechaActividad
     * @return Actividad
     */
    public function setFechaActividad($fechaActividad)
    {
        $this->fechaActividad = $fechaActividad;

        return $this;
    }

    /**
     * Get fechaActividad
     *
     * @return \DateTime 
     */
    public function getFechaActividad()
    {
        return $this->fechaActividad;
    }

    /**
     * Set desde
     *
     * @param \DateTime $desde
     * @return Actividad
     */
    public function setDesde($desde)
    {
        $this->desde = $desde;

        return $this;
    }

    /**
     * Get desde
     *
     * @return \DateTime 
     */
    public function getDesde()
    {
        return $this->desde;
    }

    /**
     * Set hasta
     *
     * @param \DateTime $hasta
     * @return Actividad
     */
    public function setHasta($hasta)
    {
        $this->hasta = $hasta;

        return $this;
    }

    /**
     * Get hasta
     *
     * @return \DateTime 
     */
    public function getHasta()
    {
        return $this->hasta;
    }

    /**
     * Set tipoAcceso
     *
     * @param integer $tipoAcceso
     * @return Actividad
     */
    public function setTipoAcceso($tipoAcceso)
    {
        $this->tipoAcceso = $tipoAcceso;

        return $this;
    }

    /**
     * Get tipoAcceso
     *
     * @return integer 
     */
    public function getTipoAcceso()
    {
        return $this->tipoAcceso;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Actividad
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set residencial
     *
     * @param \Richpolis\BackendBundle\Entity\Residencial $residencial
     * @return Actividad
     */
    public function setResidencial(\Richpolis\BackendBundle\Entity\Residencial $residencial = null)
    {
        $this->residencial = $residencial;

        return $this;
    }

    /**
     * Get residencial
     *
     * @return \Richpolis\BackendBundle\Entity\Residencial 
     */
    public function getResidencial()
    {
        return $this->residencial;
    }

    /**
     * Add edificios
     *
     * @param \Richpolis\BackendBundle\Entity\Edificio $edificios
     * @return Actividad
     */
    public function addEdificio(\Richpolis\BackendBundle\Entity\Edificio $edificios)
    {
        $this->edificios[] = $edificios;

        return $this;
    }

    /**
     * Remove edificios
     *
     * @param \Richpolis\BackendBundle\Entity\Edificio $edificios
     */
    public function removeEdificio(\Richpolis\BackendBundle\Entity\Edificio $edificios)
    {
        $this->edificios->removeElement($edificios);
    }

    /**
     * Get edificios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEdificios()
    {
        return $this->edificios;
    }
    
    /**
     * @Assert\True(message = "Los horarios no son correctos")
     */
    public function isHorarioValido()
    {
        return ($this->desde < $this->hasta);
    }
}
