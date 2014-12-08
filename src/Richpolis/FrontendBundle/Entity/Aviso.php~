<?php

namespace Richpolis\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Aviso
 *
 * @ORM\Table(name="avisos")
 * @ORM\Entity(repositoryClass="Richpolis\FrontendBundle\Repository\AvisoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Aviso
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
     * @Assert\NotBlank(message="Ingresa el titulo del aviso")
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="aviso", type="text")
     */
    private $aviso;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="tipo_aviso", type="integer")
     */
    private $tipoAviso;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_acceso", type="integer")
     */
    private $tipoAcceso;

    /**
     * @var \Residencial
     * @todo Residencial del aviso
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\BackendBundle\Entity\Residencial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="residencial_id", referencedColumnName="id")
     * })
     */
    private $residencial;
    
    /**
     * @var integer
     * @todo Edificios dentro de la residencial avisos. 
     *
     * @ORM\ManyToMany(targetEntity="Richpolis\BackendBundle\Entity\Edificio")
     * @ORM\JoinTable(name="avisos_edificios")
     * @ORM\OrderBy({"nombre" = "ASC"})
     */
    private $edificios;
    
    /**
     * @var \Usuario
     * @todo Usuario del aviso
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\BackendBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private $link;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="enviar_email", type="boolean", nullable=true)
     */
    private $enviarEmail = false;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime",nullable=true)
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
	
    const TIPO_AVISO=1;
    const TIPO_NOTIFICACION=2;
        
    static public $sTipoAcceso=array(
        self::TIPO_ACCESO_RESIDENCIAL=>'Residencial',
        self::TIPO_ACCESO_EDIFICIO=>'Edificio',
        self::TIPO_ACCESO_PRIVADO=>'Privado',
    );
	
    static public $sTipoAviso = array(
	self::TIPO_AVISO => 'Aviso',
	self::TIPO_NOTIFICACION => 'Notificacion'
    );
    
    public function getStringTipoAviso(){
        return self::$sTipoAviso[$this->getTipoAviso()];
    }
    
    static function getArrayTipoAviso(){
        return self::$sTipoAviso;
    }
	
    static function getPreferedTipoAviso(){
        return array(self::TIPO_AVISO);
    }
	
    public function getStringTipoAcceso(){
        return self::$sTipoAcceso[$this->getTipoAcceso()];
    }
    
    static function getArrayTipoAcceso(){
        return self::$sTipoAcceso;
    }
	
    static function getPreferedTipoAcceso(){
        return array(self::TIPO_ACCESO_RESIDENCIAL);
    }
	
    public function __construct(){
        $this->edificios = new \Doctrine\Common\Collections\ArrayCollection();
	$this->tipoAviso = self::TIPO_AVISO;
	$this->tipoAcceso = self::TIPO_ACCESO_RESIDENCIAL;
        $this->enviarEmail = false;
    }
    
    public function getStringNivel(){
        switch($this->getTipoAcceso()){
            case self::TIPO_ACCESO_RESIDENCIAL:
                return 'Aviso nivel residencial';
            case self::TIPO_ACCESO_EDIFICIO: 
                return 'Aviso por edificio ';
            case self::TIPO_ACCESO_PRIVADO:
                return 'Aviso nivel usuario: ' . $this->getUsuario()->getNumero() . ' - ' .
                    $this->getUsuario()->getNombre();
                    
        }
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
     * @return Aviso
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
     * Set aviso
     *
     * @param string $aviso
     * @return Aviso
     */
    public function setAviso($aviso)
    {
        $this->aviso = $aviso;

        return $this;
    }

    /**
     * Get aviso
     *
     * @return string 
     */
    public function getAviso()
    {
        return $this->aviso;
    }

    /**
     * Set tipoAviso
     *
     * @param integer $tipoAviso
     * @return Aviso
     */
    public function setTipoAviso($tipoAviso)
    {
        $this->tipoAviso = $tipoAviso;

        return $this;
    }

    /**
     * Get tipoAviso
     *
     * @return integer 
     */
    public function getTipoAviso()
    {
        return $this->tipoAviso;
    }

    /**
     * Set tipoAcceso
     *
     * @param integer $tipoAcceso
     * @return Aviso
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
     * Set link
     *
     * @param string $link
     * @return Aviso
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Aviso
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
     * @return Aviso
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
     * @return Aviso
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
     * Set usuario
     *
     * @param \Richpolis\BackendBundle\Entity\Usuario $usuario
     * @return Aviso
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
     * Set enviarEmail
     *
     * @param boolean $enviarEmail
     * @return Aviso
     */
    public function setEnviarEmail($enviarEmail)
    {
        $this->enviarEmail = $enviarEmail;

        return $this;
    }

    /**
     * Get enviarEmail
     *
     * @return boolean 
     */
    public function getEnviarEmail()
    {
        return $this->enviarEmail;
    }
}
