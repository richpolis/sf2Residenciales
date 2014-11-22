<?php

namespace Richpolis\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Richpolis\BackendBundle\Entity\Edificio;
use Richpolis\BackendBundle\Entity\Usuario;
use Richpolis\FrontendBundle\Entity\Aviso;

/**
 * AvisoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AvisoRepository extends EntityRepository
{
    public function queryFindAvisosPorEdificio(Edificio $edificio) {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT a,e,r "
                . "FROM FrontendBundle:Aviso a "
                . "JOIN a.edificio e "
                . "JOIN a.residencial r "
                . "WHERE (e.id=:edificio OR r.id =:residencial) "
                . "AND a.tipoAcceso<=:tipoAcceso "
                . "ORDER BY a.createdAt DESC");
        $consulta->setParameters(array(
            'edificio' => $edificio->getId(),
            'residencial' => $edificio->getResidencial()->getId(),
            'tipoAcceso' => Aviso::TIPO_ACCESO_EDIFICIO,
        ));
        return $consulta;
    }

    public function findAvisosPorEdificio(Edificio $edificio) {
        return $this->queryFindAvisosPorEdificio($edificio)->getResult();
    }
    
    public function queryFindAvisosPorUsuario(Usuario $usuario) {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT a,e,r "
                . "FROM FrontendBundle:Aviso a "
                . "JOIN a.usuario u "
                . "JOIN a.edificio e "
                . "JOIN a.residencial r "
                . "WHERE (e.id=:edificio OR r.id =:residencial OR u.id =:usuario) "
                . "AND a.tipoAcceso<=:tipoAcceso "
                . "ORDER BY a.createdAt DESC");
        $consulta->setParameters(array(
            'usuario' => $usuario->getId(),
            'edificio' => $usuario->getEdificio()->getId(),
            'residencial' => $usuario->getEdificio()->getResidencial()->getId(),
            'tipoAcceso' => Aviso::TIPO_ACCESO_PRIVADO,
        ));
        return $consulta;
    }

    public function findAvisosPorUsuario(Usuario $usuario) {
        return $this->queryFindAvisosPorUsuario($usuario)->getResult();
    }
}
