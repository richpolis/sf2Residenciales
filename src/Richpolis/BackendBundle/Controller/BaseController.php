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
    
    protected function getUsuarioActual() {
        $em = $this->getDoctrine()->getManager();
        $filters = $this->getFilters();
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $usuarioId = $filters['usuario'];
            $usuario = $em->getRepository('BackendBundle:Usuario')->find($usuarioId);
            return $usuario;
        } else {
            return $this->getUser();
        }
    }

    public function getNombreMes($month) {
        switch ($month) {
            case 1: return "Enero";
            case 2: return "Febrero";
            case 3: return "Marzo";
            case 4: return "Abril";
            case 5: return "Mayo";
            case 6: return "Junio";
            case 7: return "Julio";
            case 8: return "Agosto";
            case 9: return "Septiembre";
            case 10: return "Octubre";
            case 11: return "Noviembre";
            case 12: return "Diciembre";
        }
    }
    
    protected function getRecursoActual() {
        $em = $this->getDoctrine()->getManager();
        $filters = $this->getFilters();
        if (isset($filters['recurso'])) {
            $recursoId = $filters['recurso'];
        } else {
			$residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        	$edificioActual = $this->getEdificioActual();
            $recursos = $em->getRepository('BackendBundle:Recurso')
							->getRecursosPorEdificio($edificioActual->getId(),$residencialActual->getId());
            if(count($recursos)>0){
                $filters['recurso']=$recursos[0]->getId();
                $this->setFilters($filters);
                $recursoId = $filters['recurso'];
            }else{
                $recursoId = 0;
            }
        }
        $recurso = $em->getRepository('BackendBundle:Recurso')->find($recursoId);
        return $recurso;
    }
    
    protected function getUsuariosPorEdificios($edificios) 
    {
        $usuarios = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($edificios as $edificio) {
            foreach ($edificio->getUsuarios() as $usuario) {
                $usuarios[] = $usuario;
            }
        }
        return $usuarios;
    }
    
    protected function getArregloUsuarios($usuarios){
        $arreglo = array();
        foreach($usuarios as $usuario){
            $arreglo[] = $usuario->getEmail();
        }
        return $arreglo;
    }
    
    protected function setSecurePassword(&$entity) {
        // encoder
        $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
        $passwordCodificado = $encoder->encodePassword(
                    $entity->getPassword(),
                    $entity->getSalt()
        );
        $entity->setPassword($passwordCodificado);
    }
    
    protected function enviarUsuarioCreado($sUsuario, $sPassword, $usuario) {
        $asunto = 'Usuario creado';
        $isNew = true;
        $message = \Swift_Message::newInstance()
                ->setSubject($asunto)
                ->setFrom('no-reply@mosaicors.com')
                ->setTo($usuario->getEmail())
                ->setBody(
                $this->renderView('FrontendBundle:Default:enviarCorreo.html.twig', 
                        compact('usuario', 'sUsuario', 'sPassword', 'isNew', 'asunto')), 
                'text/html'
                );
        $this->get('mailer')->send($message);
    }
    
    protected function enviarUsuarioUpdate($sUsuario, $sPassword, $usuario) {
        $asunto = 'Usuario actualizado';
        $isNew = false;
        $message = \Swift_Message::newInstance()
                ->setSubject($asunto)
                ->setFrom('no-reply@mosaicors.com')
                ->setTo($usuario->getEmail())
                ->setBody(
                $this->renderView('FrontendBundle:Default:enviarCorreo.html.twig', 
                        compact('usuario', 'sUsuario', 'sPassword', 'isNew', 'asunto')), 
                'text/html'
        );
        $this->get('mailer')->send($message);
    }
}