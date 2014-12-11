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
     * @var \Pago
     * @todo Pago del cargo
     *
     * @ORM\ManyToOne(targetEntity="Pago", inversedBy="cargos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pago_id", referencedColumnName="id")
     * })
     */
    private $pago;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_paid", type="boolean", nullable=true)
     */
    private $isPaid = false;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_acumulable", type="boolean", nullable=true)
     */
    private $isAcumulable = true;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="aviso_enviado", type="boolean", nullable=true)
     */
    private $avisoEnviado = false;
    
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
    
    const TIPO_CARGO_ANTERIOR = 0;
    const TIPO_CARGO_NORMAL = 1;
    const TIPO_CARGO_ADEUDO = 2;
    const TIPO_CARGO_RESERVACION = 3;
    const TIPO_CARGO_MULTA = 4;
    const TIPO_CARGO_MTTO = 5;
    const TIPO_CARGO_PAGO = 6;

    static public $sTipoCargo = array(
        self::TIPO_CARGO_ANTERIOR => 'Anterior',
        self::TIPO_CARGO_NORMAL => 'Normal',
        self::TIPO_CARGO_ADEUDO => 'Por adeudo',
        self::TIPO_CARGO_RESERVACION => 'Reservación',
        self::TIPO_CARGO_MULTA => 'Multa',
        self::TIPO_CARGO_MTTO => 'Mantenimiento',
        self::TIPO_CARGO_PAGO => 'Pago',
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
    
    /*
     * Constructor
     */
    public function __construct() {
        $this->isPaid       = false;
        $this->isAcumulable = true;
		$this->avisoEnviado = false;
    }
	
	private $edificios = array();
 	public function getEdificios(){
		return $this->edificios;
 	}
	public function setEdificios($edificios){
		$this->edificios = $edificios;
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
     * Set monto
     *
     * @param string $monto
     * @return EstadoCuenta
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set tipoCargo
     *
     * @param integer $tipoCargo
     * @return EstadoCuenta
     */
    public function setTipoCargo($tipoCargo)
    {
        $this->tipoCargo = $tipoCargo;

        return $this;
    }

    /**
     * Get tipoCargo
     *
     * @return integer 
     */
    public function getTipoCargo()
    {
        return $this->tipoCargo;
    }

    /**
     * Set isPaid
     *
     * @param boolean $isPaid
     * @return EstadoCuenta
     */
    public function setIsPaid($isPaid)
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    /**
     * Get isPaid
     *
     * @return boolean 
     */
    public function getIsPaid()
    {
        return $this->isPaid;
    }

    /**
     * Set isAcumulable
     *
     * @param boolean $isAcumulable
     * @return EstadoCuenta
     */
    public function setIsAcumulable($isAcumulable)
    {
        $this->isAcumulable = $isAcumulable;

        return $this;
    }

    /**
     * Get isAcumulable
     *
     * @return boolean 
     */
    public function getIsAcumulable()
    {
        return $this->isAcumulable;
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

    /**
     * Set pago
     *
     * @param \Richpolis\FrontendBundle\Entity\Pago $pago
     * @return EstadoCuenta
     */
    public function setPago(\Richpolis\FrontendBundle\Entity\Pago $pago = null)
    {
        $this->pago = $pago;

        return $this;
    }

    /**
     * Get pago
     *
     * @return \Richpolis\FrontendBundle\Entity\Pago 
     */
    public function getPago()
    {
        return $this->pago;
    }

    /**
     * Set avisoEnviado
     *
     * @param boolean $avisoEnviado
     * @return EstadoCuenta
     */
    public function setAvisoEnviado($avisoEnviado)
    {
        $this->avisoEnviado = $avisoEnviado;

        return $this;
    }

    /**
     * Get avisoEnviado
     *
     * @return boolean 
     */
    public function getAvisoEnviado()
    {
        return $this->avisoEnviado;
    }
}
