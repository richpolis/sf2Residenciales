<?php

namespace Richpolis\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Usuarios
 *
 * @ORM\Table(name="usuarios")
 * @ORM\Entity(repositoryClass="Richpolis\BackendBundle\Repository\UsuariosRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 */
class Usuario implements UserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     * @Assert\Email(message="El email {{value}} no es correcto")
     */
    private $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;
   
    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", length=255, nullable=true)
     */
    private $imagen;
    
    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=100, nullable=true)
     */
    private $numero;
    
    /**
     * @var \Edificio
     * @todo Edificio del usuario
     *
     * @ORM\ManyToOne(targetEntity="Edificio", inversedBy="usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edificio_id", referencedColumnName="id")
     * })
     */
    private $edificio;    
    
    /**
     * @var string
     *
     * @ORM\Column(name="grupo", type="integer")
     */
    private $grupo;
    
    /**
     * @var \Booolean
     *
     * @ORM\Column(name="is_active", type="boolean",nullable=true)
     */
    private $isActive = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;
    
    /**
     * @var integer
     * @todo Residenciales asociadas al usuario administrador. 
     *
     * @ORM\ManyToMany(targetEntity="Richpolis\BackendBundle\Entity\Residencial")
     * @ORM\JoinTable(name="usuarios_residencial")
     * @ORM\OrderBy({"nombre" = "ASC"})
     */
    private $residenciales;
    
    const GRUPO_USUARIOS    =   1;
    const GRUPO_ADMIN       =   2;
    const GRUPO_SUPER_ADMIN =   3;
    
    public function __toString(){
        //return sprintf('%s | %s | %s', $this->getNumero(),$this->getNombre(),$this->getEdificio());
        return $this->getNombre();
    }
    
    public function getStringCompleto(){
        return sprintf('%s | %s | %s', $this->getNombre(),$this->getNumero(),$this->getEdificio());
    }
    
    public function getStringTipoGrupo(){
        $arreglo = $this->getArrayTipoGrupo();
        return $arreglo[$this->getGrupo()];
    }
    
    static function getArrayTipoGrupo($is_super_admin=false){
        if($is_super_admin){
            $sTipoGrupo=array(
                self::GRUPO_USUARIOS=>'Usuarios',
                self::GRUPO_ADMIN=>'Administrador',
                self::GRUPO_SUPER_ADMIN=>'Superadmin',
            );
        }else{
            $sTipoGrupo=array(
                self::GRUPO_USUARIOS=>'Usuarios',
                self::GRUPO_ADMIN=>'Administrador'
            );
        }
        return $sTipoGrupo;
    }
    
    static function getPreferedTipoGrupo(){
        return array(self::GRUPO_USUARIOS);
    }

    
    public function __construct()
    {
        // may not be needed, see section on salt below
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->isActive = true;
    }
    

    /*
     * Timestable
     */
    
    /**
     ** @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(!$this->getCreatedAt())
        {
          $this->createdAt = new \DateTime();
        }
        if(!$this->getUpdatedAt())
        {
          $this->updatedAt = new \DateTime();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }
    
    public function eraseCredentials()
    {
    }
    
    public function getRoles() {
        if ($this->getGrupo() == self::GRUPO_USUARIOS) {
            return array('ROLE_USER', 'ROLE_API');
        } elseif ($this->getGrupo() == self::GRUPO_SUPER_ADMIN) {
            return array('ROLE_SUPER_ADMIN', 'ROLE_API');
        } else {
            return array('ROLE_ADMIN', 'ROLE_API');
        }
    }
    
    /**
     * Get username
     *
     * @return string | email
     */
    public function getUsername()
    {
        return $this->email;
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
        if (isset($this->imagen)) {
            // store the old name to delete after the update
            $this->temp = $this->imagen;
            $this->imagen = null;
        } else {
            $this->imagen = 'initial';
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
            $this->imagen = $filename.'.'.$this->getFile()->guessExtension();
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
        $this->getFile()->move($this->getUploadRootDir(), $this->imagen);

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
        return '/uploads/usuarios';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir();
    }
    
    public function getWebPath()
    {
        return null === $this->imagen ? null : $this->getUploadDir().'/'.$this->imagen;
    }
    
    public function getAbsolutePath()
    {
        return null === $this->imagen ? null : $this->getUploadRootDir().'/'.$this->imagen;
    }
    
    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->nombre,
            $this->numero,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->nombre,
            $this->numero,
        ) = unserialize($serialized);
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
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Usuario
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
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
     * Set email
     *
     * @param string $email
     * @return Usuario
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Usuario
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     * @return Usuario
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen
     *
     * @return string 
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return Usuario
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Usuario
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Usuario
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set edificio
     *
     * @param \Richpolis\BackendBundle\Entity\Edificio $edificio
     * @return Usuario
     */
    public function setEdificio(\Richpolis\BackendBundle\Entity\Edificio $edificio = null)
    {
        $this->edificio = $edificio;

        return $this;
    }

    /**
     * Get edificio
     *
     * @return \Richpolis\BackendBundle\Entity\Edificio 
     */
    public function getEdificio()
    {
        return $this->edificio;
    }

    /**
     * Set grupo
     *
     * @param integer $grupo
     * @return Usuario
     */
    public function setGrupo($grupo)
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo
     *
     * @return integer 
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Usuario
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add residenciales
     *
     * @param \Richpolis\BackendBundle\Entity\Residencial $residenciales
     * @return Usuario
     */
    public function addResidenciale(\Richpolis\BackendBundle\Entity\Residencial $residenciales)
    {
        $this->residenciales[] = $residenciales;

        return $this;
    }

    /**
     * Remove residenciales
     *
     * @param \Richpolis\BackendBundle\Entity\Residencial $residenciales
     */
    public function removeResidenciale(\Richpolis\BackendBundle\Entity\Residencial $residenciales)
    {
        $this->residenciales->removeElement($residenciales);
    }

    /**
     * Get residenciales
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResidenciales()
    {
        return $this->residenciales;
    }
}
