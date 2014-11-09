<?php

namespace Richpolis\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * EstadoCuenta
 *
 * @ORM\Table(name="estadodecuenta")
 * @ORM\Entity(repositoryClass="Richpolis\FrontendBundle\Repository\EstadoCuentaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EstadoCuenta
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
     * @todo cargo, descripcion del cargo realizado 
     *
     * @ORM\Column(name="cargo", type="string", length=255)
     * @Assert\NotBlank(message="Ingresa una descripcion del cargo")
     */
    private $cargo;    

    /**
     * @var decimal
     *
     * @ORM\Column(name="monto", type="decimal")
     * @Assert\NotBlank(message="Ingresa un monto a cargo")
     */
    private $monto;

    /**
     * @var integer
     * @todo Tipo de cargo aplicado, o la causa
     *
     * @ORM\Column(name="tipo_cargo", type="integer")
     */
    private $tipoCargo;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_paid", type="boolean")
     */
    private $isPaid;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="paid_at", type="date")
     */
    private $paidAt;
    
    /**
     * @var \Usuario
     * @todo Usuario del estado de cuenta
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\BackendBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;


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
    
    const TIPO_CARGO_NORMAL=1;
    const TIPO_CARGO_ADEUDO=2;
    const TIPO_CARGO_INTERESES=3;
        
    static public $sTipoCargo=array(
        self::TIPO_CARGO_NORMAL=>'Normal',
        self::TIPO_CARGO_ADEUDO=>'Por adeudo',
        self::TIPO_CARGO_INTERESES=>'Intereses',
    );
    
    public function getStringTipoCargo(){
        return self::$sTipoCargo[$this->getTipoCargo()];
    }
    
    static function getArrayTipoCargo(){
        return self::$sTipoCargo;
    }
    
    static function getPreferedTipoCargo(){
        return array(self::TIPO_CARGO_NORMAL);
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
     * Set cargo
     *
     * @param string $cargo
     * @return EstadoCuenta
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Get cargo
     *
     * @return string 
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set tipo
     *
     * @param integer $tipoCargo
     * @return EstadoCuenta
     */
    public function setTipoCargo($tipo)
    {
        $this->tipoCargo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipoCargo()
    {
        return $this->tipoCargo;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return EstadoCuenta
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
     * @return EstadoCuenta
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
}
