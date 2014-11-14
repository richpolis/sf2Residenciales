<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Richpolis\FrontendBundle\Entity\Contacto;
use Richpolis\FrontendBundle\Form\ContactoType;


class DefaultController extends BaseController {

    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $context = $this->get('security.context');
        if(true === $context->isGranted('ROLE_SUPER_ADMIN')){
            $comentarios = $em->getRepository('FrontendBundle:Comentario')
                              ->findBy(array('residencial'=>$residencial,'nivel'=>0), array('createdAt'=>'DESC'), 3);
            $cargos = $em->getRepository('FrontendBundle:EstadoCuenta')
                        ->getCargosAdeudoPorEdificio($edificio->getId());
            
        }else if(true === $context->isGranted('ROLE_ADMIN')){
            $comentarios = $em->getRepository('FrontendBundle:Comentario')
                              ->findBy(array('residencial'=>$residencial,'nivel'=>0), array('createdAt'=>'DESC'), 3);
            $cargos = $em->getRepository('FrontendBundle:EstadoCuenta')
                        ->getCargosAdeudoPorEdificio($edificio->getId());
        }else{
            //true === $this->get('security.context')->isGranted('ROLE_USUARIO')
            $comentarios = $em->getRepository('FrontendBundle:Comentario')
                              ->findBy(array('residencial'=>$residencial,'nivel'=>0,'usuario'=>$this->getUser()), 
                                      array('createdAt'=>'DESC'), 3);
        }
            $cargos = $em->getRepository('FrontendBundle:EstadoCuenta')
                     ->getCargosAdeudoPorUsuario($this->getUser()->getId());
        
        
        
        $reservaciones = $em->getRepository('FrontendBundle:Reservacion')
                            ->getReservacionesPorEdificio($edificio->getId());
        
        
        
        return array(
            'comentarios'   =>  $comentarios,
            'cargos'        =>  $cargos,
            'reservaciones' =>  $reservaciones,
        );
    }
    
    /**
     * @Route("/contacto", name="frontend_contacto")
     * @Method({"GET", "POST"})
     */
    public function contactoAction(Request $request) {
        $contacto = new Contacto();
        $form = $this->createForm(new ContactoType(), $contacto);
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $datos = $form->getData();
                $configuracion = $em->getRepository('BackendBundle:Configuraciones')
                        ->findOneBy(array('slug' => 'email-contacto'));
                $message = \Swift_Message::newInstance()
                        ->setSubject('Contacto desde pagina')
                        ->setFrom($datos->getEmail())
                        ->setTo($configuracion->getTexto())
                        ->setBody($this->renderView('FrontendBundle:Default:contactoEmail.html.twig', array('datos' => $datos)), 'text/html');
                $this->get('mailer')->send($message);
                // Redirige - Esto es importante para prevenir que el usuario
                // reenvíe el formulario si actualiza la página
                $ok = true;
                $error = false;
                $mensaje = "Se ha enviado el mensaje";
                $contacto = new Contacto();
                $form = $this->createForm(new ContactoType(), $contacto);
            } else {
                $ok = false;
                $error = true;
                $mensaje = "El mensaje no se ha podido enviar";
            }
        } else {
            $ok = false;
            $error = false;
            $mensaje = "";
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('FrontendBundle:Default:formContacto.html.twig', array(
                        'form' => $form->createView(),
                        'ok' => $ok,
                        'error' => $error,
                        'mensaje' => $mensaje,
            ));
        }

        $pagina = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina' => 'contacto'));

        return $this->render('FrontendBundle:Default:contacto.html.twig', array(
                    'form' => $form->createView(),
                    'ok' => $ok,
                    'error' => $error,
                    'mensaje' => $mensaje,
                    'pagina' => $pagina,
        ));
    }
    
    public function residencialNameAction(){
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        return $this->render('FrontendBundle:Default:residencialName.html.twig', array(
            'objeto'=>$residencial,
        ));
    }
    
    public function edificiosResidencialAction(){
        $em = $this->getDoctrine()->getManager();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $edificios = $em->getRepository('BackendBundle:Edificio')->findBy(array(
                        'residencial'=>$residencial,
                     ));
        return $this->render('FrontendBundle:Default:edificiosResidencial.html.twig', array(
            'edificios' => $edificios,
            'edificioActual' => $edificio,
        ));
    }
    
    /**
     * @Route("/cambiar/edificio", name="cambiar_edificio")
     */
    public function cambiarEdificioAction(Request $request) {
        $filtros = $this->getFilters();
        if($request->query->has('edificio') == true){
            $filtros['edificio'] = $request->query->get('edificio');
            $this->setFilters($filtros);
        }
        return $this->redirect($this->generateUrl('homepage'));
        
    }
}
