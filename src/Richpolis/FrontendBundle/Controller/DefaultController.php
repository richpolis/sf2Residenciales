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
use Richpolis\BackendBundle\Form\UsuarioFrontendType;


class DefaultController extends BaseController {

    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request) {
        
        $context = $this->get('security.context');
        if(true === $context->isGranted('ROLE_SUPER_ADMIN')){
            return $this->redirect($this->generateUrl('residenciales'));
        }else if(true === $context->isGranted('ROLE_ADMIN')){
           return $this->redirect($this->generateUrl('residenciales'));
        }else{
           return $this->usuariosIndex($request);
        }
        
    }
	
    public function adminIndex(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();

        $queryForos = $em->getRepository('FrontendBundle:Foro')
                               ->queryFindForosPorEdificio($edificio);
        
        $cargos = $em->getRepository('FrontendBundle:EstadoCuenta')
                     ->getCargosAdeudoPorEdificio($edificio->getId());
        
        $reservaciones = $em->getRepository('FrontendBundle:Reservacion')
                            ->findReservacionesPorEdificio($edificio);
        
        $queryAvisos = $em->getRepository('FrontendBundle:Aviso')
                          ->queryFindAvisosPorEdificio($edificio);
        
        return $this->render('FrontendBundle:Default:index.html.twig', array(
            'foros'   =>  $queryForos->setMaxResults(10)->getResult(),
            'avisos' => $queryAvisos->setMaxResults(10)->getResult(),
            'cargos'        =>  $cargos,
            'reservaciones' =>  $reservaciones,
        ));
    }
    
    public function usuariosIndex(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        
        $cargos = $em->getRepository('FrontendBundle:EstadoCuenta')
                     ->getCargosAdeudoPorUsuario($this->getUser()->getId());

        return $this->render('FrontendBundle:Default:index.html.twig', array(
            'cargos'        =>  $cargos,
        ));
    }
    
    public function forosIndexAction() {
        $em = $this->getDoctrine()->getManager();
        $edificio = $this->getEdificioActual();
        $foros = $em->getRepository('FrontendBundle:Foro')
                    ->findForosPorEdificio($edificio);
        return $this->render('FrontendBundle:Foro:lista.html.twig', array(
            'foros'=>$foros,
        ));
    }
    
    public function avisosIndexAction() {
        $em = $this->getDoctrine()->getManager();
        $avisos = $em->getRepository('FrontendBundle:Aviso')
                     ->findAvisosPorUsuario($this->getUser());
        return $this->render('FrontendBundle:Aviso:lista.html.twig', array(
            'avisos'=>$avisos,
        ));
    }
    
    public function reservacionesIndexAction() {
        $em = $this->getDoctrine()->getManager();

        $reservaciones = $em->getRepository('FrontendBundle:Reservacion')
                            ->findReservacionesPorUsuario($this->getUser());
        return $this->render('FrontendBundle:Reservacion:lista.html.twig', array(
            'reservaciones' =>  $reservaciones,
        ));
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
            $pagina = $request->query->get('pagina','homepage');
            $this->setFilters($filtros);
        }
        return $this->redirect($this->generateUrl($pagina));
        
    }
    
    /**
     * @Route("/recuperar",name="recuperar")
     * @Template()
     * @Method({"GET","POST"})
     */
    public function recuperarAction(Request $request)
    {   
        $sPassword = "";
        $sUsuario = "";
        $msg = "";
        if($request->isMethod('POST')){
            $email = $request->get('email');
            $usuario = $this->getDoctrine()->getRepository('BackendBundle:Usuario')
                            ->findOneBy(array('email'=>$email));
            if(!$usuario){
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'El email no esta registrado.'
                );
                return $this->redirect($this->generateUrl('recuperar'));
            }else{
                $sPassword = substr(md5(time()), 0, 7);
                $sUsuario = $usuario->getUsername();
                $encoder = $this->get('security.encoder_factory')
                            ->getEncoder($usuario);
                $passwordCodificado = $encoder->encodePassword(
                            $sPassword, $usuario->getSalt()
                );
                $usuario->setPassword($passwordCodificado);
                $this->getDoctrine()->getManager()->flush();
                
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Se ha enviado un correo con la nueva contraseña.'
                );
                
                $this->enviarRecuperar($sUsuario, $sPassword, $usuario);
                $msg = "Te llegara un mail con detalle de tu cuenta";   
            }
        }
        return array('msg'=>$msg);
    }
    
    /**
     * @Route("/registro",name="registro")
     * @Template()
     * @Method({"GET","POST"})
     */
    public function registroAction(Request $request)
    {
        $usuario = new Usuario();
        $form = $this->createForm( new UsuarioFrontendType(), $usuario);
        $isNew = true;
        if($request->isMethod('POST')){
            $parametros = $request->request->all();
            $form->handleRequest($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $this->setSecurePassword($usuario);
                $rolUsuario = $em->getRepository('UsuariosBundle:Roles')
                                 ->findOneBy(array('nombre'=>'ROLE_USUARIO'));
                $usuario->addRol($rolUsuario);
                $em->persist($usuario);
                $em->flush();
                return $this->redirect($this->generateUrl('login'));
            }
        }
        
        return array(
            'form'      =>  $form->createView(),
            'titulo'    => 'Registro',
            'usuario'   => $usuario,
            'isNew'     =>  true,
        );
    }
    
    /**
     * @Route("/perfil",name="perfil_usuario")
     * @Template("FrontendBundle:Default:registro.html.twig")
     * @Method({"GET","POST"})
     */
    public function perfilUsuarioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUser();
        if (!$usuario) {
            return $this->redirect($this->generateUrl('login'));
        }
        $form = $this->createForm(new UsuarioFrontendType(), $usuario, array(
            'em' => $this->getDoctrine()->getManager())
        );
        $isNew = false;
        if($request->isMethod('POST')){
            //obtiene la contraseña
            $current_pass = $usuario->getPassword();
            $form->handleRequest($request);
            if($form->isValid()){
                if (null == $usuario->getPassword()) {
                    // usuario no cambio contraseña
                    $usuario->setPassword($current_pass);
                } else {
                    // se actualiza la contraseña
                    $this->setSecurePassword($usuario);
                }
                $em->flush();
                $this->enviarUsuarioUpdate($usuario->getEmail(), $current_pass, $usuario);
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Se han realizado los cambios solicitados.'
                );
            }
        }
        
        return array(
            'form'      =>  $form->createView(),
            'usuario'   =>  $usuario,
            'titulo'    => 'Editar perfil',
            'isNew'     =>  $isNew,
        );
    }
    
    private function enviarRecuperar($sUsuario, $sPassword, Usuario $usuario, $isNew = false) {
        $asunto = 'Se ha reestablecido su contraseña';
        $message = \Swift_Message::newInstance()
                ->setSubject($asunto)
                ->setFrom('noreply@mosaicors.com')
                ->setTo($usuario->getEmail())
                ->setBody(
                $this->renderView('FrontendBundle:Default:enviarCorreo.html.twig', 
                        compact('usuario', 'sUsuario', 'sPassword', 'isNew', 'asunto')), 
                'text/html'
        );
        $this->get('mailer')->send($message);
    }

}
