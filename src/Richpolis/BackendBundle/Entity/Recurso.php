<?php

namespace Richpolis\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Recurso
 *
 * @ORM\Table(name="recursos")
 * @ORM\Entity(repositoryClass="Richpolis\BackendBundle\Repository\RecursoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Recurso
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
     * @Assert\NotBlank(message="Ingresa el nombre del recurso")
     */
    private $nombre;

   /**
     * @var integer
     * @todo Edificios del recurso. 
     *
     * @ORM\ManyToMany(targetEntity="Richpolis\BackendBundle\Entity\Edificio")
     * @ORM\JoinTable(name="edificios_recursos")
     * @ORM\OrderBy({"nombre" = "ASC"})
     */
    private $edificios;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipoAcceso", type="integer")
     */
    private $tipoAcceso; 

    /**
     * @var decimal
     *
     * @ORM\Column(name="precio", type="decimal", nullable=true)
     */
    private $precio;
    
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
     * @var \Booolean
     *
     * @ORM\Column(name="lunes", type="boolean",nullable=true)
     */
    private $lunes = true;
    
    /**
     * @var \Booolean
     *
     * @ORM\Column(name="martes", type="boolean",nullable=true)
     */
    private $martes = true;
    
    /**
     * @var \Booolean
     *
     * @ORM\Column(name="miercoles", type="boolean",nullable=true)
     */
    private $miercoles = true;
    
    /**
     * @var \Booolean
     *
     * @ORM\Column(name="jueves", type="boolean",nullable=true)
     */
    private $jueves = true;
    
    /**
     * @var \Booolean
     *
     * @ORM\Column(name="viernes", type="boolean",nullable=true)
     */
    private $viernes = true;
    
    /**
     * @var \Booolean
     *
     * @ORM\Column(name="sabado", type="boolean",nullable=true)
     */
    private $sabado = true;
    
    /**
     * @var \Booolean
     *
     * @ORM\Column(name="domingo", type="boolean",nullable=true)
     */
    private $domingo = true;
    

    public function __toString() {
        return $this->nombre;
    }
    
    const TIPO_ACCESO_RESIDENCIAL=1;
    const TIPO_ACCESO_EDIFICIO=2;
    const TIPO_ACCESO_PRIVADO=3;
        
    static public $sTipoAcceso=array(
        self::TIPO_ACCESO_RESIDENCIAL=>'A residencial',
        self::TIPO_ACCESO_EDIFICIO=>'Por torre',
        self::TIPO_ACCESO_PRIVADO=>'Privado (solo administración)',
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
    
    public function __construct() {
        $this->edificios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->precio = 0;
    }
    
    public function getDiasDisponibles(){
        $arreglo = array(
            '0'=>($this->getDomingo()?'Dom':''),
            '1'=>($this->getLunes()?'Lun':''),
            '2'=>($this->getMartes()?'Mar':''),
            '3'=>($this->getMiercoles()?'Mie':''),
            '4'=>($this->getJueves()?'Jue':''),
            '5'=>($this->getViernes()?'Vie':''),
            '6'=>($this->getSabado()?'Sab':''),
        );
        $cadena ="";
        $coma = false;
        foreach($arreglo as $dia){
            if(strlen($dia)>0 && strlen($cadena)>0){
                $coma = true;
            }
            $cadena .=($coma?",".$dia:$dia);
            $coma = false;
        }
        return "[$cadena]";
    }
    
    public function getHorariosDisponibles(){
        return $this->desde->format('G:ia') .' - '. $this->hasta->format('G:ia');
    }
    
    public function getStringRecurso(){
        $texto = sprintf('%s %s %s', $this->nombre,$this->getDiasDisponibles(),$this->getHorariosDisponibles());
        if($this->precio>0){
            $texto .="<br/> Cuota:$ ".number_format($this->precio, 2, '.', ',');
        }
        return $texto;
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
     * @return Recurso
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
     * Set tipoAcceso
     *
     * @param integer $tipoAcceso
     * @return Recurso
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
     * Set precio
     *
     * @param string $precio
     * @return Recurso
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return string 
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Add edificios
     *
     * @param \Richpolis\BackendBundle\Entity\Edificio $edificios
     * @return Recurso
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
     * Set desde
     *
     * @param \DateTime $desde
     * @return Recurso
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
     * @return Recurso
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
     * Set lunes
     *
     * @param boolean $lunes
     * @return Recurso
     */
    public function setLunes($lunes)
    {
        $this->lunes = $lunes;

        return $this;
    }

    /**
     * Get lunes
     *
     * @return boolean 
     */
    public function getLunes()
    {
        return $this->lunes;
    }

    /**
     * Set martes
     *
     * @param boolean $martes
     * @return Recurso
     */
    public function setMartes($martes)
    {
        $this->martes = $martes;

        return $this;
    }

    /**
     * Get martes
     *
     * @return boolean 
     */
    public function getMartes()
    {
        return $this->martes;
    }

    /**
     * Set miercoles
     *
     * @param boolean $miercoles
     * @return Recurso
     */
    public function setMiercoles($miercoles)
    {
        $this->miercoles = $miercoles;

        return $this;
    }

    /**
     * Get miercoles
     *
     * @return boolean 
     */
    public function getMiercoles()
    {
        return $this->miercoles;
    }

    /**
     * Set jueves
     *
     * @param boolean $jueves
     * @return Recurso
     */
    public function setJueves($jueves)
    {
        $this->jueves = $jueves;

        return $this;
    }

    /**
     * Get jueves
     *
     * @return boolean 
     */
    public function getJueves()
    {
        return $this->jueves;
    }

    /**
     * Set viernes
     *
     * @param boolean $viernes
     * @return Recurso
     */
    public function setViernes($viernes)
    {
        $this->viernes = $viernes;

        return $this;
    }

    /**
     * Get viernes
     *
     * @return boolean 
     */
    public function getViernes()
    {
        return $this->viernes;
    }

    /**
     * Set sabado
     *
     * @param boolean $sabado
     * @return Recurso
     */
    public function setSabado($sabado)
    {
        $this->sabado = $sabado;

        return $this;
    }

    /**
     * Get sabado
     *
     * @return boolean 
     */
    public function getSabado()
    {
        return $this->sabado;
    }

    /**
     * Set domingo
     *
     * @param boolean $domingo
     * @return Recurso
     */
    public function setDomingo($domingo)
    {
        $this->domingo = $domingo;

        return $this;
    }

    /**
     * Get domingo
     *
     * @return boolean 
     */
    public function getDomingo()
    {
        return $this->domingo;
    }
    
    public function getArregloHorasDisponible(){
        $arreglo = array();
        $horaIni = $this->desde->format('g');
        $horaFin = $this->hasta->format('g');
        
        for($cont=$horaIni; $horaIni<$horaFin;$cont++){
            $arreglo[$cont] = cont;
        }
        return $arreglo;
    }
    
    
}
