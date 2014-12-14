<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\FrontendBundle\Entity\Reservacion;
use Richpolis\FrontendBundle\Form\ReservacionType;
use Richpolis\FrontendBundle\Entity\Aviso;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
use Ps\PdfBundle\Annotation\Pdf;

/**
 * Reservacion controller.
 *
 * @Route("/reservaciones")
 */
class ReservacionController extends BaseController
{

    /**
     * Lists all Reservacion entities.
     *
     * @Route("/", name="reservaciones")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        if($this->get('security.context')->isGranted('ROLE_ADMIN')){
            return $this->adminIndex($request);
        }else{
            return $this->usuariosIndex($request);
        }
    }
    
    public function adminIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();
        
        $reservaciones = $em->getRepository('FrontendBundle:Reservacion')
                        ->findReservacionesPorEdificio($edificioActual);

        return $this->render("FrontendBundle:Reservacion:index.html.twig", array(
            'entities' => $reservaciones,
            'residencial'=>$residencialActual,
            'edificio'=>$edificioActual,
        ));
    }
    
    public function usuariosIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();

        $fecha = new \DateTime();
        $year = $request->query->get('year', $fecha->format('Y'));
        $month = $request->query->get('month', $fecha->format('m'));
        $nombreMes = $this->getNombreMes($month);

        $reservaciones = $em->getRepository('FrontendBundle:Reservacion')
                ->findReservacionesPorUsuarioPorFecha($this->getUser(), $month, $year);

        return $this->render("FrontendBundle:Reservacion:reservaciones.html.twig", array(
              'entities' => $reservaciones,
              'residencial' => $residencialActual,
              'edificio' => $edificioActual,
              'month' => $month,
              'year' => $year,
              'nombreMes' => $nombreMes,
        ));
    }
    
    /**
     * Creates a new Reservacion entity.
     *
     * @Route("/", name="reservaciones_create")
     * @Method("POST")
     * @Template("FrontendBundle:Reservacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Reservacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($entity->getMonto() == 0) {
                $entity->setIsAproved(true);
            }else{
                $entity->setIsAproved(false);
            }
            $entity->setStatus(Reservacion::STATUS_SOLICITUD);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('reservaciones_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Creates a form to create a Reservacion entity.
     *
     * @param Reservacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Reservacion $entity)
    {
        $form = $this->createForm(new ReservacionType(), $entity, array(
            'action' => $this->generateUrl('reservaciones_create'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Reservacion entity.
     *
     * @Route("/new", name="reservaciones_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $em = $this->getDoctrine()->getManager();
        $entity = new Reservacion();
        $filtros = $this->getFilters();
        $recurso = $em->find('BackendBundle:Recurso', $filtros['recurso']);
        $usuario = $em->find('BackendBundle:Usuario', $filtros['usuario']);
        $entity->setRecurso($recurso);
        $entity->setUsuario($usuario);
        $entity->setMonto($recurso->getPrecio());
        $entity->setStatus(Reservacion::STATUS_SOLICITUD);
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
            'edificio' => $this->getEdificioActual(),
        );
    }

    /**
     * Finds and displays a Reservacion entity.
     *
     * @Route("/{id}", name="reservaciones_show", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Reservacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reservacion entity.');
        }
        
        if($request->isXmlHttpRequest()){
            return $this->renderView('FrontendBundle:Reservacion:comprobante.html.twig', array(
               'entity'=>$entity, 
            ));
        }
        
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
			'edificio' => $this->getEdificioActual(),
        );
    }

    /**
     * Displays a form to edit an existing Reservacion entity.
     *
     * @Route("/{id}/edit", name="reservaciones_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Reservacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reservacion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
			'edificio' => $this->getEdificioActual(),
        );
    }

    /**
    * Creates a form to edit a Reservacion entity.
    *
    * @param Reservacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Reservacion $entity)
    {
        $form = $this->createForm(new ReservacionType(), $entity, array(
            'action' => $this->generateUrl('reservaciones_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Reservacion entity.
     *
     * @Route("/{id}", name="reservaciones_update")
     * @Method("PUT")
     * @Template("FrontendBundle:Reservacion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Reservacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reservacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('reservaciones_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
			'edificio' => $this->getEdificioActual(),
        );
    }
    /**
     * Deletes a Reservacion entity.
     *
     * @Route("/{id}", name="reservaciones_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FrontendBundle:Reservacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Reservacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('reservaciones'));
    }

    /**
     * Creates a form to delete a Reservacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reservaciones_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Seleccionar edificio para la reservacion.
     *
     * @Route("/seleccionar/edificio", name="reservaciones_select_edificio")
     * @Template("FrontendBundle:Reservacion:select.html.twig")
     */
    public function selectEdificioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('FrontendBundle:EstadoCuenta')->findAll();
        if($request->query->has('edificio')){
            $filtros = $this->getFilters();
            $filtros['edificio'] = $request->query->get('edificio');
            $this->setFilters($filtros);
            return $this->redirect($this->generateUrl('reservaciones_select_recurso'));
        }
        
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificios = $em->getRepository('BackendBundle:Edificio')
                        ->findBy(array('residencial'=>$residencialActual));
        
        return array(
            'entities'=>$edificios,
            'residencial'=>$residencialActual,
            'ruta' => 'reservaciones_select_edificio',
            'campo' => 'edificio',
            'titulo' => 'Seleccionar edificio',
            'return' => 'reservaciones',
        );
        
    }
    
    /**
     * Seleccionar recurso para reservacion.
     *
     * @Route("/seleccionar/recurso", name="reservaciones_select_recurso")
     * @Template("FrontendBundle:Reservacion:select.html.twig")
     */
    public function selectRecursoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        if($request->query->has('recurso')){
            $filtros = $this->getFilters();
            $filtros['recurso'] = $request->query->get('recurso');
            $this->setFilters($filtros);
            return $this->redirect($this->generateUrl('reservaciones_select_usuario'));
        }
        
        //$entities = $em->getRepository('FrontendBundle:EstadoCuenta')->findAll();
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $recursos = $em->getRepository('BackendBundle:Recurso')
                       ->getRecursosPorEdificio($edificio->getId(),$residencialActual->getId());
        
        return array(
            'entities'=>$recursos,
            'residencial'=>$residencialActual,
            'edificio'=> $edificio,
            'ruta' => 'reservaciones_select_recurso',
            'campo' => 'recurso',
            'titulo' => 'Seleccionar recurso',
            'return' => 'reservaciones',
        );
        
    }
    
    /**
     * Seleccionar usuario para reservacion.
     *
     * @Route("/seleccionar/usuario", name="reservaciones_select_usuario")
     * @Template("FrontendBundle:Reservacion:selectUsuario.html.twig")
     */
    public function selectUsuarioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        if($request->query->has('usuario')){
            $filtros = $this->getFilters();
            $filtros['usuario'] = $request->query->get('usuario');
            $this->setFilters($filtros);
            return $this->redirect($this->generateUrl('reservaciones_new'));
        }
        
        //$entities = $em->getRepository('FrontendBundle:EstadoCuenta')->findAll();
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $usuarios = $em->getRepository('BackendBundle:Usuario')
                       ->findBy(array('edificio'=>$edificio));
        
        return array(
            'entities'=>$usuarios,
            'residencial'=>$residencialActual,
            'edificio'=> $edificio,
            'ruta' => 'reservaciones_select_usuario',
            'campo' => 'usuario',
            'titulo' => 'Seleccionar usuario que hace la reservacion',
            'return' => 'reservaciones_select_recurso',
        );
        
    }
    
    /**
     * Calendario de reservaciones.
     *
     * @Route("/calendario", name="reservaciones_calendario")
     * @Template()
     */
    public function calendarioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();
        $recursos = $em->getRepository('BackendBundle:Recurso')
                               ->getRecursosPorEdificio($edificioActual->getId(),$residencialActual->getId());
        if($request->query->has('recurso')){
            $filtros = $this->getFilters();
            $filtros['recurso'] = $request->query->get('recurso');
            $this->setFilters($filtros);
        }
        $recursoActual = $this->getRecursoActual();
        $fecha = new \DateTime();
        $fecha->modify('-1 month');
        if($recursoActual){
            $reservaciones = $em->getRepository('FrontendBundle:Reservacion')
                                 ->findReservacionesPorRecursoReservadas($recursoActual, $fecha);
        }else{
            $reservaciones = array();
        }
        /*$recursosR = array();
        foreach($recursosEdificio as $recurso){
            $recursosR[]=array('id'=>$recurso->getId(),'nombre'=>$recurso->getNombre());
        }*/
        
        return array(
            'amenidades' => $recursos,
            'entities' => $reservaciones,
            'recurso' => $recursoActual,
        );
    }
    
    /**
     * Aprobar reservacion.
     *
     * @Route("/aprobar/reservacion", name="reservaciones_aprobar")
     * @Method({"POST"})
     */
    public function aprobarAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $reservacion = $em->find('FrontendBundle:Reservacion', $id);
        
        if($reservacion){
            $reservacion->setIsAproved(true);
            $reservacion->setStatus(Reservacion::STATUS_APROBADA);
            $em->persist($reservacion);
            $this->get('richpolis.controller.aviso')->aprobarReservacion($reservacion,$em);
        }
        $em->flush();
        
        if($request->isXmlHttpRequest()){
            return $this->renderView('FrontendBundle:Reservacion:item.html.twig', array(
               'entity'=> $reservacion,
            ));
        }
        
        return $this->redirect($this->generateUrl('reservaciones_show', array('id' => $reservacion->getId())));
    }

    /**
     * Aprobar rechazar reservacion.
     *
     * @Route("/rechazar/reservacion", name="reservaciones_rechazar")
     * @Method({"POST"})
     */
    public function rechazarAction(Request $request, $id)
    {
       $em = $this->getDoctrine()->getManager();
       $residencial = $this->getResidencialActual($this->getResidencialDefault());
       $reservacion = $em->find('FrontendBundle:Reservacion', $id);
       
       if($reservacion){
            $reservacion->setIsAproved(false);
            $reservacion->setStatus(Reservacion::STATUS_RECHAZADA);
            $em->persist($reservacion);
            $this->get('richpolis.controller.aviso')->rechazarReservacion($reservacion,$em);
       }
       $em->flush();
       
       if($request->isXmlHttpRequest()){
            return $this->renderView('FrontendBundle:Reservacion:item.html.twig', array(
               'entity'=> $reservacion,
            ));
        }
        
       return $this->redirect($this->generateUrl('reservaciones_show',array('id'=>$reservacion->getId())));
    }

    /**
     * Formulario para realizar reservacion.
     *
     * @Route("/realizar/reservacion", name="reservaciones_realizar_reservacion")
     * @Method({"GET","POST"})
     */
    public function realizarReservacionAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUsuarioActual();
        $recursoActual = $this->getRecursoActual();
        $entity = new Reservacion();
        $entity->setUsuario($usuario);
        if ($request->query->has('fecha')) {
            $fecha = new \DateTime($request->query->get('fecha'));
        } else {
            $fecha = new \DateTime();
        }
        $entity->setRecurso($recursoActual);
        $entity->setFechaEvento($fecha);
        $entity->setMonto($recursoActual->getPrecio());
        $form = $this->createCreateForm($entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entity = $form->getData();
                if($this->getValidarHorariosDisponibles($entity)){
                    $em->persist($entity);
                    $em->flush();
                    $response = new JsonResponse(json_encode(array(
                       'html' => '',
                       'respuesta' => 'creado',
                    )));
                    return $response;
                }else{
                    $response = new JsonResponse(json_encode(array(
                    'form' => $this->renderView('FrontendBundle:Pago:formPago.html.twig', array(
                          'rutaAction' => $this->generateUrl('reservaciones_realizar_reservacion'),
                          'form' => $form->createView(),
                     )),
                     'respuesta' => 'error',
                     'error'=>'La fecha y horarios no estan disponibles',   
                  )));
                  return $response;
                }
                
                
            }
        }

        $response = new JsonResponse(json_encode(array(
          'form' => $this->renderView('FrontendBundle:Pago:formPago.html.twig', array(
          		'rutaAction' => $this->generateUrl('reservaciones_realizar_reservacion'),
            	'form' => $form->createView(),
          	)),
          	'respuesta' => 'nuevo',
        )));
        return $response;
    }
    
    protected function getValidarHorariosDisponibles(Reservacion $reservacion){
        $em = $this->getDoctrine()->getManager();
        $reservaciones = $em->getRepository('FrontendBundle:Reservacion')
                            ->getReservacionesPorFechaEvento($reservacion->getFechaEvento());
        $horarios = array();
        foreach($reservaciones as $reser){
            $horarios[]= $reser->getHasta()->modify('+2 hours');
        }
        $resp = true;
        foreach($horarios as $horario){
            if($horario>$reservacion->getDesde()){
                $resp = false;
                break;
            }
        }
        return $resp;
    }
	
	/**
     * Revision automatica de reservaciones.
     *
     * @Route("/revision/automatica", name="reservaciones_revision_automatica")
     * @Template("FrontendBundle:Reservacion:revisionAutomatica.html.twig")
     */
        public function cargosAutomaticosAction(Request $request) {
            $em = $this->getDoctrine()->getManager();
            if ($request->query->has('residencial') == true) {
                $filtros['residencial'] = $request->query->get('residencial');
                $this->setFilters($filtros);
            }
            $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
            $edificios = $residencialActual->getEdificios();
            $residenciales = $this->getResidenciales();
            return array(
                'residencial' => $residencialActual,
                'conjuntos' => $residenciales,
                'torres' => $edificios,
            );
    }
	
    /**
     * Aplicar cargo normal a todos los inquilinos del edificio.
     *
     * @Route("/revision/edificio", name="reservaciones_revisar_edificio")
     */
    public function revisionEdificioAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        //agregando funciones especiales de fecha para MySQL
        $emConfig = $em->getConfiguration();
        //$emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        //$emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        //$emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');
        if ($request->request->has('edificioId') == true) {
            $filtros['edificio'] = $request->request->get('edificioId');
            $this->setFilters($filtros);
        }
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $usuarios = $em->getRepository('BackendBundle:Usuario')
                ->findBy(array('edificio' => $edificio));
        $fecha = new \DateTime();
        $fecha->modify('-1 day');
        $cont = 0;
        foreach ($usuarios as $usuario) {
            $reservaciones = $em->getRepository('FrontendBundle:Reservacion')
                    ->findReservacionesPorUsuarioSolicitadas($usuario);
            if (count($reservaciones) > 0) {
                foreach ($reservaciones as $reservacion) {
                    if (!$reservacion->getPago() && $fecha > $reservacion->getCreatedAt()) {
                        $reservacion->setIsAproved(false);
                        $reservacion->setStatus(Reservacion::STATUS_RECHAZADA);
                        $em->persist($reservacion);

                        $texto = "Reservacion: " . $reservacion->getFechaEvento()->format('d-m-Y') . "<br/>";
                        $texto .= "desde las : " . $reservacion->getDesde()->format('g:ia') . "<br/>";
                        $texto .= "hasta las : " . $reservacion->getHasta()->format('g:ia') . "<br/>";
                        $texto .= "ha sido rechazada<br/>";

                        $aviso = new Aviso();
                        $aviso->setTitulo("Reservación rechazada");
                        $aviso->setAviso($texto);
                        $aviso->setTipoAcceso(Aviso::TIPO_ACCESO_PRIVADO);
                        $aviso->setResidencial($residencial);
                        $aviso->setUsuario($reservacion->getUsuario());
                        $aviso->addEdificio($reservacion->getUsuario()->getEdificio());
                        $em->persist($aviso);
                        $cont++;
                    }
                }
            }
        }
        $em->flush();
        $response = new JsonResponse(array('revisiones' => "Reservaciones rechazadas " . $cont));
        return $response;
    }
    
    /**
     * Recibo de estado de cuenta.
     *
     * @Route("/mostrar/recibo", name="reservaciones_recibo")
     * @Pdf()
     */
   public function reciboAction(Request $request)
   {
        $em = $this->getDoctrine()->getManager();
        $format = $this->get('request')->get('_format');
        if ($request->query->has('reservacion') == true) {
            $reservacionId = $request->query->get('reservacion');
        }else{
            return new \Symfony\Component\HttpFoundation\Response("Sin contenido");
        }
       
       $reservacion = $em->find('FrontendBundle:Reservacion', $reservacionId);
       if(!$reservacion){
           throw $this->createNotFoundException('La reservacion solicitada no existe.');
       }
        
        
       return $this->render(sprintf('FrontendBundle:Reservacion:recibo.%s.twig',$format), array(
           'reservacion' => $reservacion,
       ));
   }
   
   
}
