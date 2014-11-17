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
     * @var \Edificio
     * @todo Edificio del recurso
     *
     * @ORM\ManyToOne(targetEntity="Edificio", inversedBy="recursos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edificio_id", referencedColumnName="id")
     * })
     */
    private $edificio;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipoAcceso", type="integer")
     */
    private $tipoAcceso; 

    /**
     * @var string
     *
     * @ORM\Column(name="precio", type="decimal",nullable=true)
     */
    private $precio;

    public function __toString() {
        return $this->nombre;
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
     * @param boolean $tipoAcceso
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
     * @return boolean 
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
     * Set edificio
     *
     * @param \Richpolis\BackendBundle\Entity\Edificio $edificio
     * @return Recurso
     */
    public function setEdificio(\Richpolis\BackendBundle\Entity\Edificio $edificio = null)
    {
        $this->edificio = $edificio;

        return $this;
    }

    /**
     * Get edificio
     *
     * @return \Richpolis\BackendBundle\Entity\Edificio 
     */
    public function getEdificio()
    {
        return $this->edificio;
    }
}
