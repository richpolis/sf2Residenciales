<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\FrontendBundle\Entity\Aviso;
use Richpolis\FrontendBundle\Entity\EstadoCuenta;
use Richpolis\FrontendBundle\Form\AvisoType;
use Richpolis\FrontendBundle\Form\AvisoPorEdificioType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Aviso controller.
 *
 * @Route("/avisos")
 */
class AvisoController extends BaseController
{

    /**
     * Lists all Aviso entities.
     *
     * @Route("/", name="avisos")
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
    
    public function adminIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();

        $buscar = $request->query->get('buscar', '');

        if (strlen($buscar) > 0) {
            $options = array('filterParam' => 'buscar', 'filterValue' => $buscar);
        } else {
            $options = array();
        }
        $query = $em->getRepository('FrontendBundle:Aviso')
                ->queryFindAvisosPorEdificio($edificioActual, $buscar);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1), 10, $options
        );

        return $this->render("FrontendBundle:Aviso:index.html.twig", array(
            'pagination' => $pagination,
            'residencial' => $residencialActual,
            'edificio' => $edificioActual,
        ));
    }
    
    public function usuariosIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();

        $fecha = new \DateTime();
        $year = $request->query->get('year', $fecha->format('Y'));
        $month = $request->query->get('month', $fecha->format('m'));
        $nombreMes = $this->getNombreMes($month);

        $avisos = $em->getRepository('FrontendBundle:Aviso')
                ->findAvisosPorUsuarioPorFecha($this->getUser(), $month, $year);

        return $this->render("FrontendBundle:Aviso:avisos.html.twig", array(
                    'entities' => $avisos,
                    'edificio' => $edificio,
                    'residencial' => $residencialActual,
                    'month' => $month,
                    'year' => $year,
                    'nombreMes' => $nombreMes,
        ));
    }
    
    /**
     * Creates a new Aviso entity.
     *
     * @Route("/", name="avisos_create")
     * @Method("POST")
     * @Template("FrontendBundle:Aviso:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Aviso();
        $filtros = $this->getFilters();
        $entity->setTipoAcceso($filtros['nivel_aviso']);
        $entity->setResidencial($this->getResidencialActual($this->getResidencialDefault()));
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->enviarCorreos($entity);
            return $this->redirect($this->generateUrl('avisos_show', array('id' => $entity->getId())));
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Creates a form to create a Aviso entity.
     *
     * @param Aviso $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Aviso $entity)
    {
        if($entity->getTipoAcceso() == Aviso::TIPO_ACCESO_EDIFICIO){
            $formType = new AvisoPorEdificioType($entity->getResidencial());
        }else{
            $formType = new AvisoType();
        }
        $form = $this->createForm($formType, $entity, array(
            'action' => $this->generateUrl('avisos_create'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Aviso entity.
     *
     * @Route("/new", name="avisos_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Aviso();
        $filtros = $this->getFilters();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $usuario = null;
        if($filtros['nivel_aviso']==Aviso::TIPO_ACCESO_PRIVADO){
            $usuario = $em->find('BackendBundle:Usuario', $filtros['usuario']);
        }
        $entity->setTipoAcceso($filtros['nivel_aviso']);
        $entity->setResidencial($residencial);
        $entity->addEdificio($this->getEdificioActual());
        $entity->setUsuario($usuario);
        $form = $this->createCreateForm($entity);
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Finds and displays a Aviso entity.
     *
     * @Route("/{id}", name="avisos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Aviso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Aviso entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Aviso entity.
     *
     * @Route("/{id}/edit", name="avisos_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Aviso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Aviso entity.');
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
    * Creates a form to edit a Aviso entity.
    *
    * @param Aviso $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Aviso $entity)
    {
        if($entity->getTipoAcceso() == Aviso::TIPO_ACCESO_EDIFICIO){
            $formType = new AvisoPorEdificioType($entity->getResidencial());
        }else{
            $formType = new AvisoType();
        }
        $form = $this->createForm($formType, $entity, array(
            'action' => $this->generateUrl('avisos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Aviso entity.
     *
     * @Route("/{id}", name="avisos_update")
     * @Method("PUT")
     * @Template("FrontendBundle:Aviso:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Aviso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Aviso entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('avisos_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores'     => RpsStms::getErrorMessages($editForm),
        );
    }
    /**
     * Deletes a Aviso entity.
     *
     * @Route("/{id}", name="avisos_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FrontendBundle:Aviso')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Aviso entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('avisos'));
    }

    /**
     * Creates a form to delete a Aviso entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('avisos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Seleccionar tipo acceso del aviso.
     *
     * @Route("/seleccionar/nivel", name="avisos_select_nivel")
     * @Template("FrontendBundle:Reservacion:select.html.twig")
     */
    public function selectNivelAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('FrontendBundle:EstadoCuenta')->findAll();
        if($request->query->has('nivel_aviso')){
            $filtros = $this->getFilters();
            $filtros['nivel_aviso'] = $request->query->get('nivel_aviso');
            $this->setFilters($filtros);
            switch($filtros['nivel_aviso']){
                case Aviso::TIPO_ACCESO_RESIDENCIAL:
                case Aviso::TIPO_ACCESO_EDIFICIO:
                    return $this->redirect($this->generateUrl('avisos_new'));
                case Aviso::TIPO_ACCESO_PRIVADO:
                    return $this->redirect($this->generateUrl('avisos_select_usuario'));
            }
        }
        
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        
        $arreglo = array(
            array('id'=>  Aviso::TIPO_ACCESO_RESIDENCIAL,'nombre'=>'Residencial'),
            array('id'=>  Aviso::TIPO_ACCESO_EDIFICIO,'nombre'=>'Por torre'),
            array('id'=>  Aviso::TIPO_ACCESO_PRIVADO,'nombre'=>'A usuario'),
        );
        
        return array(
            'entities'=>$arreglo,
            'residencial'=>$residencialActual,
            'ruta' => 'avisos_select_nivel',
            'campo' => 'nivel_aviso',
            'titulo' => 'Seleccionar nivel del aviso',
            'return' => 'avisos',
        );
        
    }
    
    /**
     * Seleccionar usuario para aviso.
     *
     * @Route("/seleccionar/usuario", name="avisos_select_usuario")
     * @Template("FrontendBundle:Reservacion:selectUsuario.html.twig")
     */
    public function selectUsuarioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        if($request->query->has('usuario')){
            $filtros = $this->getFilters();
            $filtros['usuario'] = $request->query->get('usuario');
            $this->setFilters($filtros);
            return $this->redirect($this->generateUrl('avisos_new'));
        }
        
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $usuarios = $em->getRepository('BackendBundle:Usuario')
                       ->findBy(array('edificio'=>$edificio));
        return array(
            'entities'=>$usuarios,
            'residencial'=>$residencialActual,
            'edificio'=> $edificio,
            'ruta' => 'avisos_select_usuario',
            'campo' => 'usuario',
            'titulo' => 'Aviso para usuario: seleccionar usuario',
            'return' =>'avisos'
        );
        
    }
    
    public function enviarCorreos(Aviso $entity){
        if($entity->getEnviarEmail()){
            switch($entity->getTipoAcceso()){
                case Aviso::TIPO_ACCESO_RESIDENCIAL:
                    $usuarios = $this->getDoctrine()->getRepository('BackendBundle:Usuario')
                                    ->findBy(array('residencial'=>$entity->getResidencial()));
                    break;
                case Aviso::TIPO_ACCESO_EDIFICIO:
                    $usuarios = $this->getUsuariosPorEdificios($entity->getEdificios());
                    break;
                case Aviso::TIPO_ACCESO_PRIVADO:
                    $usuarios = array($entity->getUsuario());
                    break;
            }
            
        }
    }
    
    private function enviarAvisosPorCorreo(Aviso $entity,$usuarios) {
        $asunto = $entity->getTitulo();
        $message = \Swift_Message::newInstance()
                ->setSubject($asunto)
                ->setFrom('noreply@mosaicors.com')
                ->setTo($this->getArregloUsuarios($usuarios))
                ->setBody(
                $this->renderView('FrontendBundle:Default:enviarAviso.html.twig', 
                        compact('entity')), 
                'text/html'
        );
        $this->get('mailer')->send($message);
    }
	
	/**
     * Aplicar avisos de cargos del mes.
     *
     * @Route("/aplicar/avisos/cargos", name="avisos_avisos_cargos")
     */
    public function aplicarAvisosCargosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //agregando funciones especiales de fecha para MySQL
        $emConfig = $em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');
	if($request->request->has('edificioId') == true){
            $filtros['edificio'] = $request->request->get('edificioId');
            $this->setFilters($filtros);
        }
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $usuarios = $em->getRepository('BackendBundle:Usuario')
                       ->findBy(array('edificio'=>$edificio));
        $fecha = new \DateTime();
        $mes = $fecha->format("m");
        $year = $fecha->format("Y");
        $cont = 0;
        $nombreMes = $this->getNombreMes($mes);
	$monto = 0;
        $cont = 0;
        foreach($usuarios as $usuario){
            $cargos = $em->getRepository('FrontendBundle:EstadoCuenta')
                         ->getCargosEnMes($mes,$year,$usuario);
			foreach($cargos as $cargo){
				$monto+=$cargo->getMonto();
			}
			
            if($monto>0){
                $aviso = new Aviso();
                $aviso->setTitulo("Estado de cuenta de ".$nombreMes." del ".$year);
                $aviso->setAviso("Su estado de cuenta es de ".number_format(''));
                $aviso->setTipoAcceso(Aviso::TIPO_ACCESO_PRIVADO);
                $aviso->setTipoAviso(Aviso::TIPO_NOTIFICACION);
                $aviso->setResidencial($residencial);
                $aviso->addEdificio($edificio);
                $aviso->setUsuario($usuario);
                $em->persist($aviso);
		$cont++;
            }
        }
        $em->flush();
        $response = new JsonResponse(array('avisos'=>"Avisos creados ".$cont));
        return $response;
    }
}
