<?php

namespace Richpolis\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
/**
 * Comentario
 *
 * @ORM\Table(name="comentarios")
 * @ORM\Entity(repositoryClass="Richpolis\FrontendBundle\Repository\ComentarioRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Comentario
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
     * @var \Foro
     * @todo Foro del comentario
     *
     * @ORM\ManyToOne(targetEntity="Foro", inversedBy="comentarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="foro_id", referencedColumnName="id")
     * })
     */
    private $foro;
    
    /**
     * @var \Usuario
     * @todo Usuario del edificio
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
     * @ORM\Column(name="comentario", type="text")
     */
    private $comentario;

    /**
     * @var integer
     *
     * @ORM\Column(name="nivel", type="integer")
     */
    private $nivel;
    
    /**
     * @ORM\ManyToOne(targetEntity="Richpolis\FrontendBundle\Entity\Comentario", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="Richpolis\FrontendBundle\Entity\Comentario", mappedBy="parent")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $children;

    /**
     * @var \Boolean
     *
     * @ORM\Column(name="is_administrador", type="boolean", nullable=true)
     */
    private $isAdmin;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
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
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->nivel = 0;
        $this->isAdmin = false;
    }
    
    public function getFechaTwitter(){
        return RpsStms::twitter_time($this->createdAt->format('Y-m-d H:i:s'));
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
     * Set comentario
     *
     * @param string $comentario
     * @return Comentario
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
     * Set nivel
     *
     * @param integer $nivel
     * @return Comentario
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;

        return $this;
    }

    /**
     * Get nivel
     *
     * @return integer 
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * Set isAdmin
     *
     * @param boolean $isAdmin
     * @return Comentario
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Get isAdmin
     *
     * @return boolean 
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Comentario
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
     * Set foro
     *
     * @param \Richpolis\FrontendBundle\Entity\Foro $foro
     * @return Comentario
     */
    public function setForo(\Richpolis\FrontendBundle\Entity\Foro $foro = null)
    {
        $this->foro = $foro;

        return $this;
    }

    /**
     * Get foro
     *
     * @return \Richpolis\FrontendBundle\Entity\Foro 
     */
    public function getForo()
    {
        return $this->foro;
    }

    /**
     * Set usuario
     *
     * @param \Richpolis\BackendBundle\Entity\Usuario $usuario
     * @return Comentario
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
     * Set parent
     *
     * @param \Richpolis\FrontendBundle\Entity\Comentario $parent
     * @return Comentario
     */
    public function setParent(\Richpolis\FrontendBundle\Entity\Comentario $parent = null)
    {
        $this->parent = $parent;
        
        $this->setNivel($this->parent->getNivel()+1);

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Richpolis\FrontendBundle\Entity\Comentario 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \Richpolis\FrontendBundle\Entity\Comentario $children
     * @return Comentario
     */
    public function addChild(\Richpolis\FrontendBundle\Entity\Comentario $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Richpolis\FrontendBundle\Entity\Comentario $children
     */
    public function removeChild(\Richpolis\FrontendBundle\Entity\Comentario $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }
}
