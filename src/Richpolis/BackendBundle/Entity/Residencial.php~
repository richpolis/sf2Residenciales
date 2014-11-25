<?php

namespace Richpolis\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Residencial
 *
 * @ORM\Table(name="residenciales")
 * @ORM\Entity(repositoryClass="Richpolis\BackendBundle\Repository\ResidencialRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Residencial implements \Serializable
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
     * @Assert\NotBlank(message="Ingresar nombre de residencial")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="morosidad", type="decimal")
     */
    private $morosidad;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_morosidad", type="integer")
     */
    private $tipoMorosidad;
    
    const TIPO_MOROSIDAD_PORCENTAJE=1;
    const TIPO_MOROSIDAD_PRECIO=2;
        
    static public $sTipoMorosidad=array(
        self::TIPO_MOROSIDAD_PORCENTAJE=>'Porcentaje',
        self::TIPO_MOROSIDAD_PRECIO=>'Precio',
    );

    /**
     * @var Array 
     * @todo Arreglo de edificios dentro de la residiencial
     *
     * @ORM\OneToMany(targetEntity="Edificio",mappedBy="residencial")
     * @ORM\OrderBy({"nombre" = "ASC"})
     */
    private $edificios;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->edificios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tipoMorosidad = self::TIPO_MOROSIDAD_PORCENTAJE;
    }
    
    public function __toString() {
        return $this->nombre;
    }
    
    public function getStringTipoMorosidad(){
        return self::$sTipoMorosidad[$this->getTipoMorosidad()];
    }
    static function getArrayTipoMorosidad(){
        return self::$sTipoMorosidad;
    }
    static function getPreferedTipoMorosidad(){
        return array(self::TIPO_MOROSIDAD_PORCENTAJE);
    }
    
    public function getStringMorosidad(){
        if($this->getTipoMorosidad()==self::TIPO_MOROSIDAD_PORCENTAJE){
            return $this->getMorosidad() . "%";
        }else{
            return "$ ".number_format($this->getMorosidad(),2,".",",");
        }
    }
    
    public function getAplicarMorosidadAMonto($monto = 0){
        $valorFinal = 0.0;
        if($monto > 0 ){
            if($this->getTipoMorosidad()==self::TIPO_MOROSIDAD_PORCENTAJE){
                $valorMorosidad = ($this->getMorosidad / 100);
                $valorFinal = $monto * $valorMorosidad; 
            }else{
                $valorFinal = $this->getMorosidad();
            }
        }
        return $valorFinal;
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
     * @return Residencial
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
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->nombre,
            $this->morosidad,
            $this->tipoMorosidad
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->nombre,
            $this->morosidad,
            $this->tipoMorosidad
        ) = unserialize($serialized);
    }


    /**
     * Add edificios
     *
     * @param \Richpolis\BackendBundle\Entity\Edificio $edificios
     * @return Residencial
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
     * Set morosidad
     *
     * @param string $morosidad
     * @return Residencial
     */
    public function setMorosidad($morosidad)
    {
        $this->morosidad = $morosidad;

        return $this;
    }

    /**
     * Get morosidad
     *
     * @return string 
     */
    public function getMorosidad()
    {
        return $this->morosidad;
    }

    /**
     * Set tipoMorosidad
     *
     * @param integer $tipoMorosidad
     * @return Residencial
     */
    public function setTipoMorosidad($tipoMorosidad)
    {
        $this->tipoMorosidad = $tipoMorosidad;

        return $this;
    }

    /**
     * Get tipoMorosidad
     *
     * @return integer 
     */
    public function getTipoMorosidad()
    {
        return $this->tipoMorosidad;
    }
}
