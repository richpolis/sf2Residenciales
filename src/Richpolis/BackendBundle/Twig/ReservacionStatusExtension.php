<?php

namespace Richpolis\BackendBundle\Twig;

use Richpolis\FrontendBundle\Entity\Reservacion;

class ReservacionStatusExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'reservacionStatus' => new \Twig_Filter_Method($this, 'reservacionStatusFilter'),
        );
    }

    public function reservacionStatusFilter($status)
    {
        switch($status){
            case Reservacion::STATUS_SOLICITUD:
                $label = '<span class="label label-default">En solicitud</span>';
                break;
            case Reservacion::STATUS_APROBADA:
                $label = '<span class="label label-primary">Aprobada</span>';
                break;
            case Reservacion::STATUS_RECHAZADA:
                $label = '<span class="label label-danger">Rechazada</span>';
                break;
        }
        return $label;
    }

    public function getName()
    {
        return "reservacion_status_extension";
    }
}