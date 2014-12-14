<?php

namespace Richpolis\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints  as Assert;

/**
 * Reservacion
 *
 * @ORM\Table(name="reservaciones")
 * @ORM\Entity(repositoryClass="Richpolis\FrontendBundle\Repository\ReservacionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Reservacion
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
     * @var \Recurso
     * @todo Recurso solicitado
     *
     * @ORM\ManyToOne(targetEntity="Richpolis\BackendBundle\Entity\Recurso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recurso_id", referencedColumnName="id")
     * })
     */
    private $recurso;

    /**
     * @var \Usuario
     * @todo Usuario que solicito la reservacion
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
     * @ORM\Column(name="fechaEvento", type="date",nullable=true)
     * @Assert\NotBlank(message="Ingresa la fecha del evento")
     * @Assert\Date()
     */
    private $fechaEvento;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="desde", type="time",nullable=true)
     */
    private $desde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hasta", type="time",nullable=true)
     */
    private $hasta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_aproved", type="boolean", nullable=true)
     */
    private $isAproved = false;
	
    /**
     * @var decimal
     *
     * @ORM\Column(name="monto", type="decimal", nullable=true)
     */
    private $monto;
	
    /**
     * @var \Pago
     * @todo Pago de la reservacion
     *
     * @ORM\ManyToOne(targetEntity="Pago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pago_id", referencedColumnName="id")
     * })
     */
    private $pago;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;
    
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
    
    const STATUS_SOLICITUD=1;
    const STATUS_APROBADA=2;
    const STATUS_RECHAZADA=3;
        
    static public $sStatus=array(
        self::STATUS_SOLICITUD=>'En solicitud',
        self::STATUS_APROBADA=>'Aprobada',
        self::STATUS_RECHAZADA=>'Rechazada',
    );
    
    public function getStringStatus(){
        return self::$sStatus[$this->getStatus()];
    }
    
    static function getArrayStatus(){
        return self::$sStatus;
    }
    
    static function getPreferedStatus(){
        return array(self::STATUS_SOLICITUD);
    }
    
    public function __construct() {
        $this->isAproved = false;
		$this->monto = 0;
        $this->status = Reservacion::STATUS_SOLICITUD;
    }
    
    public function getStringReservacion(){
        return sprintf("Recurso %s reservacion por usuario %s - %s", 
                $this->getRecurso(),$this->getUsuario()->getNumero(),$this->getUsuario());
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
     * Set fechaEvento
     *
     * @param \DateTime $fechaEvento
     * @return Reservacion
     */
    public function setFechaEvento($fechaEvento)
    {
        $this->fechaEvento = $fechaEvento;

        return $this;
    }

    /**
     * Get fechaEvento
     *
     * @return \DateTime 
     */
    public function getFechaEvento()
    {
        return $this->fechaEvento;
    }

    /**
     * Set desde
     *
     * @param \DateTime $desde
     * @return Reservacion
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
     * @return Reservacion
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
     * Set isAproved
     *
     * @param boolean $isAproved
     * @return Reservacion
     */
    public function setIsAproved($isAproved)
    {
        $this->isAproved = $isAproved;

        return $this;
    }

    /**
     * Get isAproved
     *
     * @return boolean 
     */
    public function getIsAproved()
    {
        return $this->isAproved;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return Reservacion
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;
        
        if($monto==0){
            $this->isAproved = true;
        }

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Reservacion
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
     * Set recurso
     *
     * @param \Richpolis\BackendBundle\Entity\Recurso $recurso
     * @return Reservacion
     */
    public function setRecurso(\Richpolis\BackendBundle\Entity\Recurso $recurso = null)
    {
        $this->recurso = $recurso;

        return $this;
    }

    /**
     * Get recurso
     *
     * @return \Richpolis\BackendBundle\Entity\Recurso 
     */
    public function getRecurso()
    {
        return $this->recurso;
    }

    /**
     * Set usuario
     *
     * @param \Richpolis\BackendBundle\Entity\Usuario $usuario
     * @return Reservacion
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
     * @return Reservacion
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
     * Set status
     *
     * @param integer $status
     * @return Reservacion
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * @Assert\True(message = "Los horarios no son correctos")
     */
    public function isHorarioValido()
    {
        return ($this->desde < $this->hasta);
    }
	
	/**
     * @Assert\True(message = "Este dia no esta diponible para reservación")
     */
    public function isDiasOperativos()
    {
        switch ($this->fechaEvento->format('w')) {
            case 0: return $this->getRecurso()->getDomingo();
            case 1: return $this->getRecurso()->getLunes();
            case 2: return $this->getRecurso()->getMartes();
            case 3: return $this->getRecurso()->getMiercoles();
            case 4: return $this->getRecurso()->getJueves();
            case 5: return $this->getRecurso()->getViernes();
            case 6: return $this->getRecurso()->getSabado();
        }
    }
	
	/**
     * @Assert\True(message = "No esta disponible en estos horarios")
     */
    public function isHorarioOperativo()
    {
        $recurso = $this->getRecurso();
        $desde = false; $hasta = false;
        if($recurso->getDesde() >= $this->desde){
            $desde = true;
        }
        if($this->hasta <= $recurso->getHasta()){
            $hasta = true;
        }
        return ($desde && $hasta);
    }
}
