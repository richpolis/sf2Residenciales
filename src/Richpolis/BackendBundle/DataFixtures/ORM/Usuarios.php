<?php

/*
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 * 
 * Modificado por Ricardo Alcantara <richpolis@gmail.com>
 *
 */

namespace Richpolis\BackendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Richpolis\BackendBundle\Entity\Usuario;

/**
 * Fixtures de la entidad Usuario.
 */
class Usuarios extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public function getOrder()
    {
        return 30;
    }

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        // Obtener las residenciales creadas
        $residencias = $manager->getRepository('BackendBundle:Residencial')
                               ->findAll();
        $residencial1 = $residencias[0];
        $residencial2 = $residencias[1];

        // Superadmin
        $superadmin = new Usuario();
        
        //$superadmin->setUsername('admin');
        $superadmin->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = 'admin';
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($superadmin);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $superadmin->getSalt());
        $superadmin->setPassword($passwordCodificado);
        $superadmin->setNombre("Administrador General");
        $superadmin->setEmail('admin@residenciales.com');
        $superadmin->setTelefono('55555555');
        $superadmin->setNumero('000');
        $superadmin->setGrupo(Usuario::GRUPO_SUPER_ADMIN);
        $manager->persist($superadmin);
        
        // Superadmin 2
        $superadmin = new Usuario();
        
        //$superadmin->setUsername('admin');
        $superadmin->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = 'mosaicomeuvera9210290';
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($superadmin);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $superadmin->getSalt());
        $superadmin->setPassword($passwordCodificado);
        $superadmin->setNombre("Administrador General");
        $superadmin->setEmail('admin@mosaicors.com');
        $superadmin->setTelefono('55555555');
        $superadmin->setNumero('000');
        $superadmin->setGrupo(Usuario::GRUPO_SUPER_ADMIN);
        $manager->persist($superadmin);
        
        // Administrador residencial 1
        $AdminR1 = new Usuario();
        
        //$AdminR1->setUsername('RESIDENCIAL_1');
        $AdminR1->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = 'RESIDENCIAL_1';
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($AdminR1);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $AdminR1->getSalt());
        $AdminR1->setPassword($passwordCodificado);
        $AdminR1->setNombre("Administrador");
        $AdminR1->setEmail('administrador1@residenciales.com');
        $AdminR1->setTelefono('55555555');
        $AdminR1->setNumero('000');
        $AdminR1->addResidenciale($residencial1);
        $AdminR1->addResidenciale($residencial2);
        $AdminR1->setGrupo(Usuario::GRUPO_ADMIN);
        $manager->persist($AdminR1);
        
        
        // Inquilino edificio 1a depto 101, residencial 1
        $inquilino = new Usuario();
        
        //$inquilino->setUsername('R1_E1A_D101');
        $inquilino->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = '12345678';
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($inquilino);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $inquilino->getSalt());
        $inquilino->setPassword($passwordCodificado);
        $inquilino->setNombre("Inquilino Depto 101");
        $inquilino->setEmail('usuario1@residenciales.com');
        $inquilino->setTelefono('55555555');
        $inquilino->setNumero('101');
        $edificios = $residencial1->getEdificios();
        $inquilino->setEdificio($edificios[1]);
        $inquilino->setGrupo(Usuario::GRUPO_USUARIOS);
        $manager->persist($inquilino);
        
        
        // Inquilino edificio 1a depto 101, residencial 2
        //$inquilino = new Usuario();
        
        //$inquilino->setUsername('R2_E1A_D101');
        $inquilino->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = '12345678';
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($inquilino);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $inquilino->getSalt());
        $inquilino->setPassword($passwordCodificado);
        $inquilino->setNombre("Inquilino Depto 102");
        $inquilino->setEmail('r2_e1a_d101@app.residenciales.com');
        $inquilino->setTelefono('55555555');
        $inquilino->setNumero('102');
        $edificios = $residencial2->getEdificios();
        $inquilino->setEdificio($edificios[1]);
        $inquilino->setGrupo(Usuario::GRUPO_USUARIOS);
        $manager->persist($inquilino);

        $manager->flush();
    }

    
}