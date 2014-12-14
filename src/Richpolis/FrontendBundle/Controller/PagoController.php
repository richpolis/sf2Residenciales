<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\FrontendBundle\Entity\Pago;
use Richpolis\FrontendBundle\Form\PagoType;
use Richpolis\FrontendBundle\Form\PagoFrontendType;
use Richpolis\FrontendBundle\Entity\EstadoCuenta;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Pago controller.
 *
 * @Route("/pagos")
 */
class PagoController extends BaseController
{

    /**
     * Lists all Pago entities.
     *
     * @Route("/", name="pagos")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        if($this->get('security.context')->isGranted('ROLE_ADMIN')){
            return $this->adminIndex($request);
        }else{
            return $this->usuariosIndex($request);
        }
    }
    
    public function adminIndex(Request $request){
        $em = $this->getDoctrine()->getManager();

        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();
        
        $buscar = $request->query->get('buscar','');
        
        if(strlen($buscar)>0){
            $options = array('filterParam'=>'buscar','filterValue'=>$buscar);
        }else{
            $options = array();
        }
        $query = $em->getRepository('FrontendBundle:Pago')
                              ->queryFindPagos($buscar,$edificioActual->getId());
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, $this->get('request')->query->get('page', 1),10, $options 
        );
        
        return $this->render("FrontendBundle:Pago:index.html.twig", array(
            'pagination' => $pagination,
            'residencial'=> $residencialActual,
            'edificio' => $edificioActual,
        ));
    }
    
    public function usuariosIndex(Request $request){
        $em = $this->getDoctrine()->getManager();

        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();
        
		$fecha = new \DateTime();
		$year = $request->query->get('year', $fecha->format('Y'));
        $month = $request->query->get('month', $fecha->format('m'));
		$nombreMes = $this->getNombreMes($month);
		
        $pagos = $em->getRepository('FrontendBundle:Pago')
                    ->findPagosPorUsuarioPorFecha($this->getUser(),$month,$year);
        
        return $this->render("FrontendBundle:Pago:pagos.html.twig", array(
            'entities' => $pagos,
            'residencial'=> $residencialActual,
            'edificio' => $edificioActual,
			'month'=>$month,
			'year'=>$year,
			'nombreMes' => $nombreMes,
        ));
    }
    
    /**
     * Creates a new Pago entity.
     *
     * @Route("/", name="pagos_create")
     * @Method("POST")
     * @Template("FrontendBundle:Pago:new.html.twig")
     */
    public function createAction(Request $request)
    {
        
        $entity = new Pago();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $usuario = $this->getUsuarioActual();
            //solo cargos por pagar
            $cargos = $em->getRepository('FrontendBundle:EstadoCuenta')
                    ->getCargosAdeudoPorUsuario($usuario->getId(), false);
            foreach ($cargos as $cargo) {
                $cargo->setPago($entity);
                $em->persist($cargo);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('pagos_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Creates a form to create a Pago entity.
     *
     * @param Pago $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pago $entity)
    {
        $form = $this->createForm(new PagoType(), $entity, array(
            'action' => $this->generateUrl('pagos_create'),
            'method' => 'POST',
            'em'=>$this->getDoctrine()->getManager(),
			
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pago entity.
     *
     * @Route("/new", name="pagos_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUsuarioActual();
        //solo cargos por pagar
        $cargos = $em->getRepository('FrontendBundle:EstadoCuenta')
                ->getCargosAdeudoPorUsuario($usuario->getId(), false);
        $monto = 0;
        foreach ($cargos as $cargo) {
            $monto += $cargo->getMonto();
        }
        $entity = new Pago();
        $entity->setUsuario($usuario);
        $entity->setMonto($monto);
                
        $form = $this->createCreateForm($entity);

        return array(
            'entity'    => $entity,
            'form'      => $form->createView(),
            'errores'   => RpsStms::getErrorMessages($form),
            'cargos'    => $cargos,
            'monto'     => $monto,
        );
    }

    /**
     * Finds and displays a Pago entity.
     *
     * @Route("/{id}", name="pagos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Pago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pago entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Pago entity.
     *
     * @Route("/{id}/edit", name="pagos_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Pago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pago entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
        );
    }

    /**
    * Creates a form to edit a Pago entity.
    *
    * @param Pago $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pago $entity)
    {
        $form = $this->createForm(new PagoType(), $entity, array(
            'action' => $this->generateUrl('pagos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em'=>$this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Pago entity.
     *
     * @Route("/{id}", name="pagos_update")
     * @Method("PUT")
     * @Template("FrontendBundle:Pago:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Pago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pago entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pagos_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
        );
    }
    /**
     * Deletes a Pago entity.
     *
     * @Route("/{id}", name="pagos_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FrontendBundle:Pago')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pago entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pagos'));
    }

    /**
     * Creates a form to delete a Pago entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pagos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Aprobar pago.
     *
     * @Route("/aprobar/{id}", name="pagos_aprobar")
     */
    public function aprobarAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $pago = $em->find('FrontendBundle:Pago', $id);
        $monto = 0;
        foreach ($pago->getCargos() as $cargo) {
            $cargo->setIsPaid(true);
            $monto += $cargo->getMonto();
            if($cargo->getTipoCargo()==EstadoCuenta::TIPO_CARGO_RESERVACION){
                $reservacion = $em->getRepository('FrontendBundle:Reservacion')
                                  ->findOneBy(array('pago'=>$pago));
                if($reservacion){
                    $reservacion->setIsAproved(true);
                    $em->persist($reservacion);
                    $aviso = new \Richpolis\FrontendBundle\Entity\Aviso();
                    $aviso->setTitulo("Estado de cuenta de " . $nombreMes . " del " . $year);
                    $aviso->setAviso("Su estado de cuenta es de $ " . number_format($monto, 2, '.', ','));
                    $aviso->setTipoAcceso(Aviso::TIPO_ACCESO_PRIVADO);
                    $aviso->setTipoAviso(Aviso::TIPO_NOTIFICACION);
                    $aviso->setResidencial($residencial);
                    $aviso->addEdificio($edificio);
                    $aviso->setUsuario($usuario);
                    $em->persist($aviso);
                }
            }
            $em->persist($cargo);
        }
        $pago->setIsAproved(true);
        if ($monto > 0) {
            //se realiza el cargo de pago
            $cargo = new EstadoCuenta();
            $cargo->setCargo("Gracias por su pago");
            $cargo->setMonto(($monto - ($monto * 2)));
            $cargo->setUsuario($pago->getUsuario());
            //$cargo->setResidencial($residencial);
            $cargo->setTipoCargo(EstadoCuenta::TIPO_CARGO_PAGO);
            $cargo->setIsAcumulable(true);
            $cargo->setIsPaid(true);
            $em->persist($cargo);
            $em->flush();
            $pago->addCargo($cargo);
        }
        $em->flush();
        return $this->redirect($this->generateUrl('pagos_show', array('id' => $pago->getId())));
    }

    /**
     * Rechazar pago.
     *
     * @Route("/rechazar/{id}", name="pagos_rechazar")
     */
    public function rechazarAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $pago = $em->find('FrontendBundle:Pago', $id);

        foreach ($pago->getCargos() as $cargo) {
            $cargo->setIsPaid(true);
            $em->persist($cargo);
            $em->flush();
        }
        $pago->setIsAproved(false);
        $em->flush();
        return $this->redirect($this->generateUrl('pagos_show', array('id' => $pago->getId())));
    }

    /**
     * Seleccionar usuario para pago.
     *
     * @Route("/seleccionar/usuario", name="pagos_select_usuario")
     * @Template("FrontendBundle:Reservacion:selectUsuario.html.twig")
     */
    public function selectUsuarioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        if($request->query->has('usuario')){
            $filtros = $this->getFilters();
            $filtros['usuario'] = $request->query->get('usuario');
            $this->setFilters($filtros);
            return $this->redirect($this->generateUrl('pagos_new'));
        }
        
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $usuarios = $em->getRepository('BackendBundle:Usuario')
                       ->findBy(array('edificio'=>$edificio));
        
        return array(
            'entities'=>$usuarios,
            'residencial'=>$residencialActual,
            'edificio'=> $edificio,
            'ruta' => 'pagos_select_usuario',
            'campo' => 'usuario',
            'titulo' => 'Seleccionar usuario para pago',
            'return' => 'pagos'
        );
        
    }
    
    /**
     * Exportar los pagos.
     *
     * @Route("/exportar", name="pagos_exportar")
     */
    public function exportarAction(Request $request)
    {
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        
        $pagos = $this->getDoctrine()
                ->getRepository('FrontendBundle:Pago')
                ->findPagos("",$edificio->getId());

        $response = $this->render(
                'FrontendBundle:Pago:list.xls.twig', array('entities' => $pagos)
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export-pagos.xls"');
        return $response;
    }
	
    /**
     * Formulario para realizar pago.
     *
     * @Route("/realizar/pago", name="pagos_realizar_pago")
     * @Method({"GET","POST"})
     */
    public function realizarPagoAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUsuarioActual();
        //solo cargos por pagar
        $cargos = $em->getRepository('FrontendBundle:EstadoCuenta')
                ->getCargosAdeudoPorUsuario($usuario->getId(), false);
        $monto = 0;
        foreach ($cargos as $cargo) {
            $monto += $cargo->getMonto();
        }
        $entity = new Pago();
        $entity->setUsuario($usuario);
        $entity->setMonto($monto);
        $entity->setIsAproved(false);
        $entity->setReferencia($usuario->getFolioDePago());
        $form = $this->createForm(new PagoFrontendType(), $entity, array(
            'action' => $this->generateUrl('pagos_realizar_pago'),
            'method' => 'POST',
            'em'=>$this->getDoctrine()->getManager(),
        ));
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entity = $form->getData();
                $entity->setIsAproved(false);
                $em->persist($entity);
                $em->flush();
                foreach ($cargos as $cargo) {
                    $cargo->setPago($entity);
                    $em->persist($cargo);
                    $em->flush();
                }
                $response = new JsonResponse(json_encode(array(
                    'html' => '',
                    'respuesta' => 'creado',
                )));
                return $response;
            }
        }

        $response = new JsonResponse(json_encode(array(
                    'form' => $this->renderView('FrontendBundle:Pago:formPago.html.twig', array(
                        'rutaAction' => $this->generateUrl('pagos_realizar_pago'),
                        'form' => $form->createView(),
                    )),
                    'respuesta' => 'nuevo',
        )));
        return $response;
    }
    
    /**
     * Formulario para realizar pago.
     *
     * @Route("/realizar/pago/reservacion", name="pagos_realizar_pago_reservacion")
     * @Method({"GET","POST"})
     */
    public function realizarPagoReservacionAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUsuarioActual();
        $reservacionId = $request->query->get('reservacion');
        $reservacion = $em->getRepository('FrontendBundle:Reservacion')->find($reservacionId);
        $entity = new Pago();
        $entity->setUsuario($usuario);
        $entity->setMonto($reservacion->getMonto());
        $entity->setIsAproved(false);
        $entity->setReferencia($usuario->getFolioDePago());
        $form = $this->createForm(new PagoFrontendType(), $entity, array(
            'action' => $this->generateUrl('pagos_realizar_pago_reservacion'),
            'method' => 'POST',
            'em'=>$this->getDoctrine()->getManager(),
        ));
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $pago = $form->getData();
                $pago->setIsAproved(false);
                $em->persist($pago);
                $em->flush();
                //aplicamos la morosidad de la residencial
                $cargo = new EstadoCuenta();
                $cargo->setCargo("Cargo por reservacion evento dia: ". $reservacion->getFechaEvento()->format('d-m-Y') . " a las ". $reservacion->getDesde()->format('g:ia'));
                $cargo->setMonto($reservacion->getMonto());
                $cargo->setUsuario($usuario);
                $cargo->setTipoCargo(EstadoCuenta::TIPO_CARGO_RESERVACION);
                $cargo->setIsAcumulable(false);
                $cargo->setPago($pago);
                $em->persist($cargo);
                $reservacion->setPago($pago);
                $em->flush();
                $response = new JsonResponse(json_encode(array(
                    'html' => '',
                    'respuesta' => 'creado',
                )));
                return $response;
            }
        }
        $response = new JsonResponse(json_encode(array(
            'form' => $this->renderView('FrontendBundle:Pago:formPago.html.twig', array(
                'rutaAction' => $this->generateUrl('pagos_realizar_pago_reservacion'),
                'form' => $form->createView(),
            )),
            'respuesta' => 'nuevo',
        )));
        return $response;
    }

}
