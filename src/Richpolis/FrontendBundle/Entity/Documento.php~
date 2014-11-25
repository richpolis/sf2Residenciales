<?php

namespace Richpolis\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Documento
 *
 * @ORM\Table(name="documentos")
 * @ORM\Entity(repositoryClass="Richpolis\FrontendBundle\Repository\DocumentoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Documento
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
     * @Assert\NotBlank(message="Ingresa un titulo al documento")
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="archivo", type="string", length=255)
     */
    private $archivo;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_archivo", type="integer",nullable=false)
     */
    private $tipoArchivo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tipo_acceso", type="integer",nullable=false)
     */
    private $tipoAcceso;

    /**
     * @var \Residencial
     * @todo Administrador de la residencial
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
     * @ORM\ManyToMany(targetEntity="Richpolis\BackendBundle\Entity\Edificio", inversedBy="documentos")
     * @ORM\JoinTable(name="documentos_edificios")
     * @ORM\OrderBy({"nombre" = "DESC"})
     */
    private $edificios;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime",nullable=true)
     */
    private $createdAt;
    
    const TIPO_ACCESO_RESIDENCIAL=1;
    const TIPO_ACCESO_EDIFICIO=2;
    const TIPO_ACCESO_PRIVADO=3;
	
    static public $sTipoAcceso=array(
        self::TIPO_ACCESO_RESIDENCIAL=>'Residencial',
        self::TIPO_ACCESO_EDIFICIO=>'Edificio',
        self::TIPO_ACCESO_PRIVADO=>'Privado',
    );

    public function getStringTipoAcceso(){
        return self::$sTipoAcceso[$this->getTipoAcceso()];
    }
	
    static function getArrayTipoAcceso(){
        return self::$sTipoAcceso;
    }
	
    static function getPreferedTipoAcceso(){
        return array(self::TIPO_ACCESO_RESIDENCIAL);
    }
    
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
            $this->setTipoArchivo(RpsStms::getTipoArchivo($this->archivo));
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
        return '/uploads/documentos';
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
    
    public function getIsImagen(){
        return $this->getTipoArchivo() == RpsStms::TIPO_ARCHIVO_IMAGEN;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->edificios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tipoAcceso = self::TIPO_ACCESO_RESIDENCIAL;
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
     * @return Documento
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Documento
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set archivo
     *
     * @param string $archivo
     * @return Documento
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
     * Set tipoArchivo
     *
     * @param integer $tipoArchivo
     * @return Documento
     */
    public function setTipoArchivo($tipoArchivo)
    {
        $this->tipoArchivo = $tipoArchivo;

        return $this;
    }

    /**
     * Get tipoArchivo
     *
     * @return integer 
     */
    public function getTipoArchivo()
    {
        return $this->tipoArchivo;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Documento
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
     * @return Documento
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
     * @return Documento
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
     * Set tipoAcceso
     *
     * @param integer $tipoAcceso
     * @return Documento
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
}
