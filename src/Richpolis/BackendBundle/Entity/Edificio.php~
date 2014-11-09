<?php

namespace Richpolis\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Usuario
 *
 * @ORM\Table(name="edificios")
 * @ORM\Entity(repositoryClass="Richpolis\BackendBundle\Repository\EdificioRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Edificio
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
     */
    private $nombre;
    
    /**
     * @var \Residencial
     * @todo Residencial del edificio
     *
     * @ORM\ManyToOne(targetEntity="Residencial", inversedBy="edificios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="residencial_id", referencedColumnName="id")
     * })
     */
    private $residencial;    

    /**
     * @var Array 
     * @todo Arreglo de recursos dentro de un recurso
     *
     * @ORM\OneToMany(targetEntity="Recurso",mappedBy="edificio")
     * @ORM\OrderBy({"nombre" = "ASC"})
     */
    private $recursos;
   
    /**
     * @var Array 
     * @todo Arreglo de usuarios dentro del edificio
     *
     * @ORM\OneToMany(targetEntity="Usuario",mappedBy="edificio")
     * @ORM\OrderBy({"nombre" = "ASC"})
     */
    private $usuarios;
    
    public function __toString() {
        return $this->nombre;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recursos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usuarios = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Usuario
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
     * Set residencial
     *
     * @param \Richpolis\BackendBundle\Entity\Residencial $residencial
     * @return Usuario
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
     * Add recursos
     *
     * @param \Richpolis\BackendBundle\Entity\Recurso $recursos
     * @return Usuario
     */
    public function addRecurso(\Richpolis\BackendBundle\Entity\Recurso $recursos)
    {
        $this->recursos[] = $recursos;

        return $this;
    }

    /**
     * Remove recursos
     *
     * @param \Richpolis\BackendBundle\Entity\Recurso $recursos
     */
    public function removeRecurso(\Richpolis\BackendBundle\Entity\Recurso $recursos)
    {
        $this->recursos->removeElement($recursos);
    }

    /**
     * Get recursos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecursos()
    {
        return $this->recursos;
    }

    /**
     * Add usuarios
     *
     * @param \Richpolis\BackendBundle\Entity\Usuario $usuarios
     * @return Usuario
     */
    public function addUsuario(\Richpolis\BackendBundle\Entity\Usuario $usuarios)
    {
        $this->usuarios[] = $usuarios;

        return $this;
    }

    /**
     * Remove usuarios
     *
     * @param \Richpolis\BackendBundle\Entity\Usuario $usuarios
     */
    public function removeUsuario(\Richpolis\BackendBundle\Entity\Usuario $usuarios)
    {
        $this->usuarios->removeElement($usuarios);
    }

    /**
     * Get usuarios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }
}
