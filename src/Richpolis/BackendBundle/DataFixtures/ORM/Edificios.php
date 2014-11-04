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
use Richpolis\BackendBundle\Entity\Edificio;

/**
 * Fixtures de la entidad Residenciales.
 * Crea dos residenciales de prueba
 */
class Edificios extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public function getOrder()
    {
        return 20;
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

       // areas comunes  residencial 1                      
        $edificio = new Edificio();
        $edificio->setNombre("Areas comunes");
        $edificio->setResidencial($residencial1);
        $manager->persist($edificio);

        // Creando 10 edificios del residencial 1
        for($cont=0;$cont<10;$cont++){                        
            $edificio = new Edificio();
            $edificio->setNombre("Edificio ".($cont+1)."A");
            $edificio->setResidencial($residencial1);
            $manager->persist($edificio);
        }

        // areas comunes  residencial 2                      
        $edificio = new Edificio();
        $edificio->setNombre("Areas comunes");
        $edificio->setResidencial($residencial2);
        $manager->persist($edificio);

        // Creando 10 edificios del residencial 2
        for($cont=0;$cont<10;$cont++){                        
            $edificio = new Edificio();
            $edificio->setNombre("Edificio ".($cont+1)."A");
            $edificio->setResidencial($residencial2);
            $manager->persist($edificio);
        }
        

        $manager->flush();   
    }

    
}
