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
 * Crea 500 usuarios de prueba con información muy realista.
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
        $residencial1 = $manager->getRepository('BackendBundle:Residencial')
                                ->findOneBy(array('username'=>'RESIDENCIAL_1'));
        $residencial2 = $manager->getRepository('BackendBundle:Residencial')
                                ->findOneBy(array('username'=>'RESIDENCIAL_2'));


        // Inquilino edificio 1a depto 101, residencial 1
        $inquilino = new Usuario();
        
        $inquilino->setUsername('R1_E1A_D101');
        $inquilino->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = 'R1_E1A_D101';
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($inquilino);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $inquilino->getSalt());
        $inquilino->setPassword($passwordCodificado);
        $inquilino->setNombre("Inquilino Edificio 1A Depto 101");
        $inquilino->setEmail('r1_e1a_d101@app.residenciales.com');
        $inquilino->setTelefono('55555555');
        $inquilino->setNumero('101');
        $edificios = $residencial1->getEdificios();
        $inquilino->setEdificio($edificios[1]);
        $manager->persist($inquilino);
        
            
        // Inquilino edificio 1a depto 101, residencial 2
        $inquilino = new Usuario();
        
        $inquilino->setUsername('R2_E1A_D101');
        $inquilino->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = 'R2_E1A_D101';
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($inquilino);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $inquilino->getSalt());
        $inquilino->setPassword($passwordCodificado);
        $inquilino->setNombre("Inquilino Edificio 1A Depto 101");
        $inquilino->setEmail('r2_e1a_d101@app.residenciales.com');
        $inquilino->setTelefono('55555555');
        $inquilino->setNumero('101');
        $edificios = $residencial1->getEdificios();
        $inquilino->setEdificio($edificios[1]);
        $manager->persist($inquilino);

        $manager->flush();
    }

    
}