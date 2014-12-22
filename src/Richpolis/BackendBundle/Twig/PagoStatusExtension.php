<?php

namespace Richpolis\BackendBundle\Twig;

use Richpolis\FrontendBundle\Entity\Pago;

class PagoStatusExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'pagoStatus' => new \Twig_Filter_Method($this, 'pagoStatusFilter'),
        );
    }

    public function pagoStatusFilter($status)
    {
        switch($status){
            case Pago::STATUS_SOLICITUD:
                $label = '<span class="label label-default">En proceso</span>';
                break;
            case Pago::STATUS_APROBADA:
                $label = '<span class="label label-primary">Aprobado</span>';
                break;
            case Pago::STATUS_RECHAZADA:
                $label = '<span class="label label-danger">Rechazado</span>';
                break;
        }
        return $label;
    }

    public function getName()
    {
        return "pago_status_extension";
    }
}