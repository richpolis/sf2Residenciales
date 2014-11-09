<?php

namespace Richpolis\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    private $residenciales = null;
    
    protected function getFilters() {
        return $this->get('session')->get('filters', array());
    }
    
    protected function setFilters($filtros) {
        $this->get('session')->set('filters', $filtros);
    }
    
    protected function getResidencialDefault() {
        $filters = $this->getFilters();
        $residencial = 0;
        if (isset($filters['residencial'])) {
            return $filters['residencial'];
        } else {
            $residenciales=$this->getResidenciales();
            if(count($residenciales)>0){
                $filters['residencial']=$residenciales[0]->getId();
                $this->setFilters($filters);
                $residencial = $residenciales[0]->getId();
            }else{
                return 0;
            }
        }
        return $residencial;
    }
    
    protected function getResidenciales() {
        if ($this->residenciales == null) {
            $em = $this->getDoctrine()->getManager();
            $this->residenciales = $em->getRepository('BackendBundle:Residencial')->findAll();
        }
        return $this->residenciales;
    }
    
    protected function getResidencialActual($residencialId) {
        $residenciales = $this->getResidenciales();
        $residencialActual = null;
        foreach ($residenciales as $residencial) {
            if ($residencial->getId() == $residencialId) {
                $residencialActual = $residencial;
                break;
            }
        }
        return $residencialActual;
    }
    
    protected function getEdificioActual() {
        $em = $this->getDoctrine()->getManager();
        $filters = $this->getFilters();
        if (isset($filters['edificio'])) {
            $edificioId = $filters['edificio'];
        } else {
            $residencial = $this->getResidencialActual($this->getResidencialDefault());
            $edificios = $em->getRepository('BackendBundle:Edificio')->findBy(array(
               'residencial'=>$residencial, 
            ));
            if(count($edificios)>0){
                $filters['edificio']=$edificios[0]->getId();
                $this->setFilters($filters);
                $edificioId = $edificios[0]->getId();
            }else{
                $edificioId = 0;
            }
        }
        
        $edificio = $em->getRepository('BackendBundle:Edificio')->find($edificioId);
        return $edificio;
    }
    
    
    
}