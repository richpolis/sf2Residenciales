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
               WHERE (e.id = :edificio AND i.tipoAcceso=:tipoAccesoEdificio ) 
               OR (r.id=:residencial AND i.tipoAcceso=:tipoAccesoResidencial )
               ORDER BY i.nombre ASC
        ')->setParameters(array(
            'edificio'=> $edificio_id,
            'residencial'=>$residencial_id,
            'tipoAccesoResidencial'=>Recurso::TIPO_ACCESO_RESIDENCIAL,
            'tipoAccesoEdificio'=>Recurso::TIPO_ACCESO_EDIFICIO,
        ));
        return $query->getResult();
    }
}
