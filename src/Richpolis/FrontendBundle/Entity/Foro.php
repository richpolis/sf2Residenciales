<?php

namespace Richpolis\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Foro
 *
 * @ORM\Table(name="foros")
 * @ORM\Entity(repositoryClass="Richpolis\FrontendBundle\Repository\ForoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Foro
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
     * @ORM\Column(name="titulo", type="string", length=255)
     * @Assert\NotBlank(message="Ingresar un titulo de la discusión")
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="comentario", type="text")
     */
    private $comentario;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_discusion", type="integer")
     */
    private $tipoDiscusion;
    
    /**
     * @var \Usuario
     * @todo usuario que creo la discusion
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\BackendBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;
    
    /**
     * @var \Edificio
     * @todo edificio que pertenece la discusion
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\BackendBundle\Entity\Edificio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edificio_id", referencedColumnName="id")
     * })
     */
    private $edificio;
    
    
    /**
     * @var Array 
     * @todo Arreglo de comentarios de una discusion
     *
     * @ORM\OneToMany(targetEntity="Richpolis\FrontendBundle\Entity\Comentario",mappedBy="foro")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $comentarios;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_cerrado", type="boolean")
     */
    private $isCerrado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetimetz")
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
    
    const TIPO_DISCUSION_PUBLICA=1;
    const TIPO_DISCUSION_PRIVADA=2;
        
    static public $sTipoDiscusion=array(
        self::TIPO_DISCUSION_PUBLICA=>'Publica',
        self::TIPO_DISCUSION_PRIVADA=>'Privada',
    );
    
    public function getStringTipoDiscusion(){
        return self::$sTipoDiscusion[$this->getTipoDiscusion()];
    }
    
    static function getArrayTipoDiscusion(){
        return self::$sTipoDiscusion;
    }
    
    static function getPreferedTipoDiscusion(){
        return array(self::TIPO_DISCUSION_PUBLICA);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->comentarios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isCerrado = false;
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
     * Set titulo
     *
     * @param string $titulo
     * @return Foro
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set comentario
     *
     * @param string $comentario
     * @return Foro
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string 
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set tipoDiscusion
     *
     * @param integer $tipoDiscusion
     * @return Foro
     */
    public function setTipoDiscusion($tipoDiscusion)
    {
        $this->tipoDiscusion = $tipoDiscusion;

        return $this;
    }

    /**
     * Get tipoDiscusion
     *
     * @return integer 
     */
    public function getTipoDiscusion()
    {
        return $this->tipoDiscusion;
    }

    /**
     * Set isCerrado
     *
     * @param boolean $isCerrado
     * @return Foro
     */
    public function setIsCerrado($isCerrado)
    {
        $this->isCerrado = $isCerrado;

        return $this;
    }

    /**
     * Get isCerrado
     *
     * @return boolean 
     */
    public function getIsCerrado()
    {
        return $this->isCerrado;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Foro
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
     * Set usuario
     *
     * @param \Richpolis\BackendBundle\Entity\Usuario $usuario
     * @return Foro
     */
    public function setUsuario(\Richpolis\BackendBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Richpolis\BackendBundle\Entity\Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set edificio
     *
     * @param \Richpolis\BackendBundle\Entity\Edificio $edificio
     * @return Foro
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

    /**
     * Add comentarios
     *
     * @param \Richpolis\FrontendBundle\Entity\Comentario $comentarios
     * @return Foro
     */
    public function addComentario(\Richpolis\FrontendBundle\Entity\Comentario $comentarios)
    {
        $this->comentarios[] = $comentarios;

        return $this;
    }

    /**
     * Remove comentarios
     *
     * @param \Richpolis\FrontendBundle\Entity\Comentario $comentarios
     */
    public function removeComentario(\Richpolis\FrontendBundle\Entity\Comentario $comentarios)
    {
        $this->comentarios->removeElement($comentarios);
    }

    /**
     * Get comentarios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComentarios()
    {
        return $this->comentarios;
    }
}
