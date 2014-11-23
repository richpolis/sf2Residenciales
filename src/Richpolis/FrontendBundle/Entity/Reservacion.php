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
     * @var integer
     *
     * @ORM\Column(name="duracion", type="integer")
     */
    private $duracion;
    
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
     * @var boolean
     *
     * @ORM\Column(name="is_aproved", type="boolean")
     */
    private $isAproved;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
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
    
    public function __construct() {
        $this->isAproved=false;
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
     * Set duracion
     *
     * @param integer $duracion
     * @return Reservacion
     */
    public function setDuracion($duracion)
    {
        $this->duracion = $duracion;

        return $this;
    }

    /**
     * Get duracion
     *
     * @return integer 
     */
    public function getDuracion()
    {
        return $this->duracion;
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
}
