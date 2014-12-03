<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\FrontendBundle\Entity\EstadoCuenta;
use Richpolis\FrontendBundle\Form\EstadoCuentaType;
use Richpolis\FrontendBundle\Form\CargoAResidencialType;
use Richpolis\FrontendBundle\Form\CargoPorEdificioType;
use Richpolis\FrontendBundle\Entity\Aviso;

use Symfony\Component\HttpFoundation\JsonResponse;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * EstadoCuenta controller.
 *
 * @Route("/cargos")
 */
class EstadoCuentaController extends BaseController
{

    /**
     * Lists all EstadoCuenta entities.
     *
     * @Route("/", name="estadodecuentas")
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
    
    public function adminIndex(Request $request){
        $em = $this->getDoctrine()->getManager();

        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();
        
        $buscar = $request->query->get('buscar','');
        $filtros = $this->getFilters();
        
        if($request->query->has('pagados')){
            $filtro = $request->query->get('pagados',false);
            $filtros['pagados'] = $filtro;
            $this->setFilters($filtros);
        }
        
        if(strlen($buscar)>0){
            $options = array('filterParam'=>'buscar','filterValue'=>$buscar);
        }else{
            $options = array();
        }
        
        $query = $em->getRepository('FrontendBundle:EstadoCuenta')
                    ->queryFindEstadoCuentas($buscar,$edificioActual->getId(),$filtros['pagados']);
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, $this->get('request')->query->get('page', 1),10, $options 
        );
        
        return $this->render("FrontendBundle:EstadoCuenta:index.html.twig", array(
            'pagination' => $pagination,
            'residencial'=> $residencialActual,
            'edificio' => $edificioActual,
            'pagados' => $filtros['pagados'],
        ));
    }
    
    public function usuariosIndex(Request $request){
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('FrontendBundle:EstadoCuenta')->findAll();
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();
        
        $todos = $request->query->get('todos',false);
        $estadodecuentas = $em->getRepository('FrontendBundle:EstadoCuenta')
                              ->getCargosAdeudoPorUsuario($this->getUser()->getId(),$todos);
        
        return $this->render("FrontendBundle:EstadoCuenta:estadodecuentas.html.twig", array(
            'entities' => $estadodecuentas,
            'residencial'=> $residencialActual,
            'edificio' => $edificioActual,
        ));
    }
    
    /**
     * Creates a new EstadoCuenta entity.
     *
     * @Route("/", name="estadodecuentas_create")
     * @Method("POST")
     * @Template("FrontendBundle:EstadoCuenta:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new EstadoCuenta();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estadodecuentas_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Creates a form to create a EstadoCuenta entity.
     *
     * @param EstadoCuenta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EstadoCuenta $entity)
    {
        
        $form = $this->createForm(new EstadoCuentaType(), $entity, array(
            'action' => $this->generateUrl('estadodecuentas_create'),
            'method' => 'POST',
            'em'=>$this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new EstadoCuenta entity.
     *
     * @Route("/new", name="estadodecuentas_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $entity = new EstadoCuenta();
        $usuario = $this->getDoctrine()->getRepository('BackendBundle:Usuario')
                                       ->find($request->query->get('usuario',0));
        if(!$usuario){
            return $this->redirect($this->generateUrl('estadodecuenta_select'));
        }
        $entity->setUsuario($usuario);
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }
    
    /**
     * Seleccionar un usuario del edificio.
     *
     * @Route("/seleccionar", name="estadodecuentas_select")
     * @Template()
     */
    public function selectAction()
    {
        $em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('FrontendBundle:EstadoCuenta')->findAll();
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();
        
        $usuarios = $em->getRepository('BackendBundle:Usuario')
                        ->findBy(array('edificio'=>$edificioActual));
        
        return array(
            'entities'=>$usuarios,
            'residencial'=>$residencialActual,
            'edificio'=>$edificioActual
        );
        
    }

    /**
     * Finds and displays a EstadoCuenta entity.
     *
     * @Route("/{id}", name="estadodecuentas_show",requirements={"id": "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:EstadoCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoCuenta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing EstadoCuenta entity.
     *
     * @Route("/{id}/edit", name="estadodecuentas_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:EstadoCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoCuenta entity.');
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
    * Creates a form to edit a EstadoCuenta entity.
    *
    * @param EstadoCuenta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EstadoCuenta $entity)
    {
        $residencial = $this->getResidencialDefault();
        $form = $this->createForm(new EstadoCuentaType(), $entity, array(
            'action' => $this->generateUrl('estadodecuentas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em'=>$this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing EstadoCuenta entity.
     *
     * @Route("/{id}", name="estadodecuentas_update")
     * @Method("PUT")
     * @Template("FrontendBundle:EstadoCuenta:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:EstadoCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstadoCuenta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estadodecuentas_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
        );
    }
    /**
     * Deletes a EstadoCuenta entity.
     *
     * @Route("/{id}", name="estadodecuentas_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FrontendBundle:EstadoCuenta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EstadoCuenta entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('estadodecuentas'));
    }

    /**
     * Creates a form to delete a EstadoCuenta entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('estadodecuentas_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Aplicar cargo normal a todos los inquilinos del edificio.
     *
     * @Route("/aplicar/cargo/normal", name="estadodecuentas_aplicar_cargo_normal")
     */
    public function aplicarCargoNormalAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //agregando funciones especiales de fecha para MySQL
        $emConfig = $em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');
        
        //$residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $usuarios = $em->getRepository('BackendBundle:Usuario')
                       ->findBy(array('edificio'=>$edificio));
        $fecha = new \DateTime();
        $mes = $fecha->format("m");
        $year = $fecha->format("Y");
        $cont = 0;
        $nombreMes = $this->getMes($mes);
        foreach($usuarios as $usuario){
            $cargo = $em->getRepository('FrontendBundle:EstadoCuenta')
                        ->getCargoEnMes($mes,$year,EstadoCuenta::TIPO_CARGO_NORMAL,$usuario);
            if(!$cargo){
                //no existe cargo en el mes, creamos el cargo
                
                $cargo = new EstadoCuenta();
                $cargo->setCargo("Cargo automatico de ".$nombreMes." del ".$year);
                $cargo->setMonto($edificio->getCuota());
                $cargo->setUsuario($usuario);
                $cargo->setTipoCargo(EstadoCuenta::TIPO_CARGO_NORMAL);
                $cargo->setIsAcumulable(true);
                $em->persist($cargo);
                $cont++;
                $aviso = new Aviso();
                $aviso->setTitulo("Cargo automatico de ".$nombreMes." del ".$year);
                $aviso->setAviso("Cargo automatico de ".$nombreMes." del ".$year);
                $aviso->setTipoAcceso(Aviso::TIPO_ACCESO_PRIVADO);
                $aviso->setTipoAviso(Aviso::TIPO_NOTIFICACION);
                $aviso->setResidencial($residencial);
                $aviso->addEdificio($edificio);
                $aviso->setUsuario($usuario);
                $em->persist($aviso);
                
            }
        }
        $em->flush();
        $response = new JsonResponse(array('cargosAplicados'=>"Cargos aplicados ".$cont));
        return $response;
    }
    
    /**
     * Aplicar cargo por adeudo.
     *
     * @Route("/aplicar/cargo/adeudo", name="estadodecuentas_aplicar_cargo_adeudo")
     */
    public function aplicarCargoAdeudoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //agregando funciones especiales de fecha para MySQL
        
        $emConfig = $em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');
        
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $usuarios = $em->getRepository('BackendBundle:Usuario')
                       ->findBy(array('edificio'=>$edificio));
        $fecha = new \DateTime();
        $mes = $fecha->format("m");
        $year = $fecha->format("Y");
        $fecha->modify("-1 month");
        $cont = 0;
        $nombreMes = $this->getMes($mes);
        foreach($usuarios as $usuario){
            $cargo = $em->getRepository('FrontendBundle:EstadoCuenta')
                        ->getCargoEnMes($mes,$year,EstadoCuenta::TIPO_CARGO_ADEUDO,$usuario);
            if(!$cargo){
                // llama a los cargos anteriores a la fecha y del usuario y que sean acumulables.
                $cargos = $em->getRepository('FrontendBundle:EstadoCuenta')
                            ->getCargosAnteriores($fecha,$usuario,true);
                if(count($cargos)>0){
                    //existen cargos en el mes
                    $monto = 0;
                    foreach($cargos as $registro){
                        $monto += $registro->getMonto();
                    }
                    //aplicamos la morosidad de la residencial
                    $cargo = new EstadoCuenta();
                    $cargo->setCargo("Cargo por adeudo del ".$nombreMes." del ".$year);
                    $cargo->setMonto($residencial->getAplicarMorosidadAMonto($monto));
                    $cargo->setUsuario($usuario);
                    $cargo->setTipoCargo(EstadoCuenta::TIPO_CARGO_ADEUDO);
                    $cargo->setIsAcumulable(true);
                    $em->persist($cargo);
                    $cont++;
                    
                    $aviso = new Aviso();
                    $aviso->setTitulo("Cargo por adeudo del ".$nombreMes." del ".$year);
                    $aviso->setAviso("Cargo por adeudo del ".$nombreMes." del ".$year);
                    $aviso->setTipoAcceso(Aviso::TIPO_ACCESO_PRIVADO);
                    $aviso->setTipoAviso(Aviso::TIPO_NOTIFICACION);
                    $aviso->setResidencial($residencial);
                    $aviso->addEdificio($edificio);
                    $aviso->setUsuario($usuario);
                    $em->persist($aviso);
                    
                }
            }
        }
        $em->flush();
        $response = new JsonResponse(array('cargosAplicados'=>"Cargos aplicados ".$cont));
        return $response;
    }
    
    public function getMes($num){
        switch($num){
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
    
    /**
     * Exportar los cargos.
     *
     * @Route("/exportar", name="estadodecuentas_exportar")
     */
    public function exportarAction(Request $request)
    {
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $filtros = $this->getFilters();
        
        $cargos = $this->getDoctrine()
                ->getRepository('FrontendBundle:EstadoCuenta')
                ->findEstadoCuentas("",$edificio->getId(),$filtros['pagadas']);

        $response = $this->render(
                'FrontendBundle:EstadoCuenta:list.xls.twig', array('entities' => $cargos)
        );
        
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export-cargos.xls"');
        return $response;
    }
    
	/**
     * Seleccionar el tipo de acceso del cargo.
     *
     * @Route("/seleccionar/tipo", name="estadodecuentas_select_tipo")
     * @Template("FrontendBundle:Reservacion:select.html.twig")
     */
    public function selectTipoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->query->has('tipo_cargo')) {
            $filtros = $this->getFilters();
            $filtros['tipo_cargo'] = $request->query->get('tipo_cargo');
            $this->setFilters($filtros);
            switch ($filtros['tipo_cargo']) {
                case 1:
                    return $this->redirect($this->generateUrl('estadodecuentas_cargos_automaticos'));
                case 2:
                    return $this->redirect($this->generateUrl('estadodecuentas_cargo_a_residencial'));
                case 3:
                    return $this->redirect($this->generateUrl('estadodecuentas_cargo_por_edificio'));
                case 4:
                    return $this->redirect($this->generateUrl('estadodecuentas_select'));
            }
        }

        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());

        $arreglo = array(
            array('id' => 1, 'nombre' => 'Cargos automaticos'),
            array('id' => 2, 'nombre' => 'Cargo general a residencial'),
            array('id' => 3, 'nombre' => 'Cuotas extraordinarias'),
            array('id' => 4, 'nombre' => 'Cargo a inquilino'),
        );

        return array(
            'entities' => $arreglo,
            'residencial' => $residencialActual,
            'ruta' => 'estadodecuentas_select_tipo',
            'campo' => 'tipo_cargo',
            'titulo' => 'Seleccionar el tipo de cargo',
            'return' => 'estadodecuentas',
        );
    }
	
	/**
     * Cargos automaticos.
     *
     * @Route("/cargos/automaticos", name="estadodecuentas_cargos_automaticos")
     * @Template("FrontendBundle:EstadoCuenta:cargosAutomaticos.html.twig")
     */
    public function cargosAutomaticosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return array();
    }
	
	/**
     * Cargo a residencial.
     *
     * @Route("/cargo/a/residencial", name="estadodecuentas_cargo_a_residencial")
     * @Template("FrontendBundle:EstadoCuenta:cargoAResidencial.html.twig")
     * @Method({"GET","POST"})
     */
    public function cargoAResidencialAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $usuarios = $em->getRepository('BackendBundle:Usuario')
                ->findUsuariosResidencial($residencial->getId());
        $entity = new EstadoCuenta();
        $form = $this->createForm(new CargoAResidencialType(), $entity, array(
            'action' => $this->generateUrl('estadodecuentas_cargo_a_residencial'),
            'method' => 'POST'
        ));
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $data = $form->getData();
            $descripcion = $data->getCargo();
            $monto = $data->getMonto();
            $montoPorUsuario = $monto / count($usuarios);
            $tipoCargo = $data->getTipoCargo();
            $isAcumulable = $data->getIsAcumulable();
            $cont = 0;
            foreach ($usuarios as $usuario) {
                $cargo = new EstadoCuenta();
                $cargo->setCargo($descripcion);
                $cargo->setMonto($montoPorUsuario);
                $cargo->setUsuario($usuario);
                $cargo->setTipoCargo($tipoCargo);
                $cargo->setIsAcumulable($isAcumulable);
                $em->persist($cargo);
                $cont++;
            }
            if($cont>0){
                $aviso = new Aviso();
                $aviso->setTitulo($descripcion);
                $aviso->setAviso($descripcion);
                $aviso->setTipoAcceso(Aviso::TIPO_ACCESO_RESIDENCIAL);
                $aviso->setTipoAviso(Aviso::TIPO_NOTIFICACION);
                $aviso->setResidencial($residencial);
                $aviso->addEdificio($this->getEdificioActual());
                $aviso->setUsuario($this->getUser());
                $em->persist($aviso);
            }
            $em->flush();
            return $this->redirect($this->generateUrl('estadodecuentas'));
        }
        return array(
            'entity' => $entity,
            'residencial' => $residencial,
            'contUsuarios' => count($usuarios),
            'form' => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }
	
	/**
     * Cargo por edificio.
     *
     * @Route("/cargo/por/edificio", name="estadodecuentas_cargo_por_edificio")
     * @Template("FrontendBundle:EstadoCuenta:cargoPorEdificio.html.twig")
     * @Method({"GET","POST"})
     */
    public function cargoPorEdificioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $entity = new EstadoCuenta();
        $form = $this->createForm(new CargoPorEdificioType($residencial), $entity, array(
            'action' => $this->generateUrl('estadodecuentas_cargo_por_edificio'),
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $data = $form->getData();
            $edificios = $data->getEdificios();
            $usuarios = $this->getUsuariosPorEdificios($edificios);
            if(count($usuarios)){
                $descripcion = $data->getCargo();
                $monto = $data->getMonto();
                $montoPorUsuario = $monto / count($usuarios);
                $tipoCargo = $data->getTipoCargo();
                $isAcumulable = $data->getIsAcumulable();
                foreach ($usuarios as $usuario) {
                    $cargo = new EstadoCuenta();
                    $cargo->setCargo($descripcion);
                    $cargo->setMonto($montoPorUsuario);
                    $cargo->setUsuario($usuario);
                    $cargo->setTipoCargo($tipoCargo);
                    $cargo->setIsAcumulable($isAcumulable);
                    $em->persist($cargo);
                }
                $aviso = new Aviso();
                $aviso->setTitulo($descripcion);
                $aviso->setAviso($descripcion);
                $aviso->setTipoAcceso(Aviso::TIPO_ACCESO_EDIFICIO);
                $aviso->setTipoAviso(Aviso::TIPO_NOTIFICACION);
                $aviso->setResidencial($residencial);
                foreach($edificios as $edificio){
                    $aviso->addEdificio($edificio);
                }
                $aviso->setUsuario($this->getUser());
                $em->persist($aviso);
                $em->flush();
            }
            return $this->redirect($this->generateUrl('estadodecuentas'));
        }
        return array(
            'entity' => $entity,
            'residencial' => $residencial,
            'form' => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }
	
	/**
     * Usuarios por edificios.
     *
     * @Route("/usuarios/por/edificio", name="estadodecuentas_usuarios_por_edificio")
     * @Method("POST")
     */
    public function usuariosPorEdificioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $entity = new EstadoCuenta();
        $form = $this->createForm(new CargoPorEdificioType($residencial), $entity, array(
            'action' => $this->generateUrl('estadodecuentas_usuarios_por_edificio'),
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $data = $form->getData();
            $usuarios = $this->getUsuariosPorEdificios($data->getEdificios());
            $response = new JsonResponse(json_encode(array('usuarios' => count($usuarios))));
            return $response;
        }
        $response = new JsonResponse(json_encode(array('usuarios' => 0)));
        return $response;
    }
	
    

}
