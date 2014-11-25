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
     * @var string
     *
     * @ORM\Column(name="precio", type="decimal", nullable=true)
     */
    private $precio;

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
}
