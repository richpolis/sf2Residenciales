<?php

/*
 * Creado por Ricardo Alcantara <richpolis@gmail.com>
 */

namespace Richpolis\BackendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Richpolis\BackendBundle\Entity\Residencial;

/**
 * Fixtures de la entidad Residencial.
 * Crea dos residenciales de prueba
 */
class Residenciales extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public function getOrder()
    {
        return 10;
    }

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        // Residencial 1
        $residencial1 = new Residencial();
        
        $residencial1->setUsername('RESIDENCIAL_1');
        $residencial1->setNombre("Residencial 1");
        $residencial1->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = 'RESIDENCIAL_1';
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($residencial1);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $residencial1->getSalt());
        $residencial1->setPassword($passwordCodificado);
        $residencial1->setPorcentaje(10);
        $manager->persist($residencial1);
        
            
        // Residencial 2
        $residencial2 = new Residencial();
        
        $residencial2->setUsername('RESIDENCIAL_2');
        $residencial2->setNombre("Residencial 2");
        $residencial2->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $passwordEnClaro = 'RESIDENCIAL_2';
        $encorder = $this->container->get('security.encoder_factory')->getEncoder($residencial2);
        $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $residencial2->getSalt());
        $residencial2->setPassword($passwordCodificado);
        $residencial2->setPorcentaje(10);
        $manager->persist($residencial2);
        
        $manager->flush();
    }

    
}