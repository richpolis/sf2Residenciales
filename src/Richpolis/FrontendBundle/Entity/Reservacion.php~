<?php

namespace Richpolis\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reservacion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Richpolis\FrontendBundle\Repository\ReservacionRepository")
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
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=255)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="recurso", type="string", length=255)
     */
    private $recurso;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaApartado", type="datetime")
     */
    private $fechaApartado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaEvento", type="datetimetz")
     */
    private $fechaEvento;

    /**
     * @var integer
     *
     * @ORM\Column(name="duracion", type="integer")
     */
    private $duracion;


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
     * Set usuario
     *
     * @param string $usuario
     * @return Reservacion
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set recurso
     *
     * @param string $recurso
     * @return Reservacion
     */
    public function setRecurso($recurso)
    {
        $this->recurso = $recurso;

        return $this;
    }

    /**
     * Get recurso
     *
     * @return string 
     */
    public function getRecurso()
    {
        return $this->recurso;
    }

    /**
     * Set fechaApartado
     *
     * @param \DateTime $fechaApartado
     * @return Reservacion
     */
    public function setFechaApartado($fechaApartado)
    {
        $this->fechaApartado = $fechaApartado;

        return $this;
    }

    /**
     * Get fechaApartado
     *
     * @return \DateTime 
     */
    public function getFechaApartado()
    {
        return $this->fechaApartado;
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
}
