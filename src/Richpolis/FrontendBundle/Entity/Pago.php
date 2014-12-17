<?php

namespace Richpolis\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Pago
 *
 * @ORM\Table(name="pagos")
 * @ORM\Entity(repositoryClass="Richpolis\FrontendBundle\Repository\PagoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Pago
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
     * @ORM\Column(name="archivo", type="string", length=255)
     */
    private $archivo;

    /**
     * @var float
     *
     * @ORM\Column(name="monto", type="float")
     */
    private $monto;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_aproved", type="boolean", nullable=true)
     */
    private $isAproved = false;
    
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
     * @var Array 
     * @todo Arreglo de cargos en un pago
     *
     * @ORM\OneToMany(targetEntity="EstadoCuenta",mappedBy="pago")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $cargos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="referencia", type="string", length=255)
     */
    private $referencia;

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
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cargos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isAproved = false;
        $this->status = Pago::STATUS_SOLICITUD;
    }

    /*** uploads ***/
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->archivo)) {
            // store the old name to delete after the update
            $this->temp = $this->archivo;
            $this->archivo = null;
        } else {
            $this->archivo = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function preUpload()
    {
      if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->archivo = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
    * @ORM\PostPersist
    * @ORM\PostUpdate
    */
    public function upload()
    {
      if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->archivo);
        

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
    * @ORM\PostRemove
    */
    public function removeUpload()
    {
      if ($file = $this->getAbsolutePath()) {
        if(file_exists($file)){
            unlink($file);
        }
      }
    }
    
    protected function getUploadDir()
    {
        return '/uploads/pagos';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir();
    }
    
    public function getWebPath()
    {
        return null === $this->archivo ? null : $this->getUploadDir().'/'.$this->archivo;
    }
    
    public function getAbsolutePath()
    {
        return null === $this->archivo ? null : $this->getUploadRootDir().'/'.$this->archivo;
    }
    
    public function getIcoArchivo(){
        return RpsStms::getIcoArchivo($this->getArchivo());
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
     * Set archivo
     *
     * @param string $archivo
     * @return Pago
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;

        return $this;
    }

    /**
     * Get archivo
     *
     * @return string 
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    /**
     * Set monto
     *
     * @param float $monto
     * @return Pago
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set isAproved
     *
     * @param boolean $isAproved
     * @return Pago
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
     * @return Pago
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
     * @return Pago
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
     * Add cargos
     *
     * @param \Richpolis\FrontendBundle\Entity\EstadoCuenta $cargos
     * @return Pago
     */
    public function addCargo(\Richpolis\FrontendBundle\Entity\EstadoCuenta $cargos)
    {
        $this->cargos[] = $cargos;

        return $this;
    }

    /**
     * Remove cargos
     *
     * @param \Richpolis\FrontendBundle\Entity\EstadoCuenta $cargos
     */
    public function removeCargo(\Richpolis\FrontendBundle\Entity\EstadoCuenta $cargos)
    {
        $this->cargos->removeElement($cargos);
    }

    /**
     * Get cargos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCargos()
    {
        return $this->cargos;
    }

    /**
     * Set referencia
     *
     * @param string $referencia
     * @return Pago
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;

        return $this;
    }

    /**
     * Get referencia
     *
     * @return string 
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Pago
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
}
