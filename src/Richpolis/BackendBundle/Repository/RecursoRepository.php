<?php

namespace Richpolis\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Richpolis\BackendBundle\Entity\Recurso;

/**
 * RecursoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RecursoRepository extends EntityRepository
{
    public function getRecursosPorEdificio($edificio_id,$residencial_id){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
               SELECT i,e,r 
               FROM BackendBundle:Recurso i 
               JOIN i.edificios e 
               JOIN e.residencial r 
               WHERE (r.id=:residencial AND i.tipoAcceso=:tipoAccesoResidencial ) 
               OR (e.id = :edificio AND i.tipoAcceso=:tipoAccesoEdificio )
               ORDER BY i.nombre ASC
        ')->setParameters(array(
            'residencial'=>$residencial_id,
            'tipoAccesoResidencial'=>Recurso::TIPO_ACCESO_RESIDENCIAL,
            'edificio'=> $edificio_id,
            'tipoAccesoEdificio'=>Recurso::TIPO_ACCESO_EDIFICIO,
        ));
        return $query->getResult();
    }
}
