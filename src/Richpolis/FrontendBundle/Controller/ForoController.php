<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\FrontendBundle\Entity\Foro;
use Richpolis\FrontendBundle\Form\ForoType;
use Richpolis\FrontendBundle\Form\ForoFrontendType;
use Richpolis\FrontendBundle\Form\ForoPorEdificioType;
use Richpolis\FrontendBundle\Form\ComentarioType;
use Richpolis\FrontendBundle\Entity\Comentario;


use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Foro controller.
 *
 * @Route("/foros")
 */
class ForoController extends BaseController
{

    /**
     * Lists all Foro entities.
     *
     * @Route("/", name="foros")
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
    
    public function adminIndex(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();

        $buscar = $request->query->get('buscar', '');

        if (strlen($buscar) > 0) {
            $options = array('filterParam' => 'buscar', 'filterValue' => $buscar);
        } else {
            $options = array();
        }
        $query = $em->getRepository('FrontendBundle:Foro')
                ->queryFindForosPorEdificio($edificioActual, $buscar);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1), 10, $options
        );

        return $this->render("FrontendBundle:Foro:index.html.twig", array(
                    'pagination' => $pagination,
                    'residencial' => $residencialActual,
                    'edificio' => $edificioActual,
        ));
    }

    public function usuariosIndex(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();

        $fecha = new \DateTime();
        $year = $request->query->get('year', $fecha->format('Y'));
        $month = $request->query->get('month', $fecha->format('m'));
        $nombreMes = $this->getNombreMes($month);

        $foros = $em->getRepository('FrontendBundle:Foro')
                ->findForosPorEdificio($edificioActual);

        return $this->render("FrontendBundle:Foro:foros.html.twig", array(
                    'entities' => $foros,
                    'residencial' => $residencialActual,
                    'edificio' => $edificioActual,
                    'month' => $month,
                    'year' => $year,
                    'nombreMes' => $nombreMes,
        ));
    }

    /**
     * Creates a new Foro entity.
     *
     * @Route("/", name="foros_create")
     * @Method("POST")
     * @Template("FrontendBundle:Foro:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Foro();
        $filtros = $this->getFilters();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        if($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $entity->setTipoAcceso($filtros['nivel_aviso']);
        }else{
            $entity->setTipoAcceso(Foro::TIPO_ACCESO_PRIVADO);
        }
        $entity->setResidencial($residencial);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('foros_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Creates a form to create a Foro entity.
     *
     * @param Foro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Foro $entity)
    {
        if($entity->getTipoAcceso() == Foro::TIPO_ACCESO_EDIFICIO){
            $formType = new ForoPorEdificioType($entity->getResidencial());
        }else{
            $formType = new ForoType();
        }
        $form = $this->createForm($formType, $entity, array(
            'action' => $this->generateUrl('foros_create'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Foro entity.
     *
     * @Route("/new", name="foros_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $entity = new Foro();
        $filtros = $this->getFilters();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        if($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $entity->setTipoAcceso($filtros['nivel_aviso']);
        }else{
            $entity->setTipoAcceso(Foro::TIPO_ACCESO_PRIVADO);
        }
        $entity->setResidencial($residencial);
        $entity->addEdificio($edificio);
        $entity->setUsuario($this->getUser());
        $form   = $this->createCreateForm($entity);
        
        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse(json_encode(array(
            'form' => $this->renderView('FrontendBundle:Foro:formForo.html.twig', array(
                'rutaAction' => $this->generateUrl('foro_crear_foro'),
                'form' => $form->createView(),
            )),
                'respuesta' => 'nuevo',
            )));
            return $response;
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Finds and displays a Foro entity.
     *
     * @Route("/{id}", name="foros_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FrontendBundle:Foro')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Foro entity.');
        }
        $deleteForm = $this->createDeleteForm($id);

        $comentario = new Comentario();
        $comentario->setForo($entity);
        $comentario->setUsuario($this->getUser());
        $comentario->setNivel(0);
        $form = $this->createForm(new ComentarioType(), $comentario, array('em' => $em));
        
        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            // formulario de comentarios
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Foro entity.
     *
     * @Route("/{id}/edit", name="foros_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Foro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Foro entity.');
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
    * Creates a form to edit a Foro entity.
    *
    * @param Foro $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Foro $entity)
    {
        if($entity->getTipoAcceso() == Foro::TIPO_ACCESO_EDIFICIO){
            $formType = new ForoPorEdificioType($entity->getResidencial());
        }else{
            $formType = new ForoType();
        }
        $form = $this->createForm($formType, $entity, array(
            'action' => $this->generateUrl('foros_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Foro entity.
     *
     * @Route("/{id}", name="foros_update")
     * @Method("PUT")
     * @Template("FrontendBundle:Foro:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Foro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Foro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('foros_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
        );
    }
    /**
     * Deletes a Foro entity.
     *
     * @Route("/{id}", name="foros_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FrontendBundle:Foro')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Foro entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('foros'));
    }

    /**
     * Creates a form to delete a Foro entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('foros_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Exportar los foros.
     *
     * @Route("/exportar", name="foros_exportar")
     */
    public function exportarAction(Request $request)
    {
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        
        $foros = $this->getDoctrine()
                ->getRepository('FrontendBundle:Pago')
                ->findForosPorEdificio("",$edificio);

        $response = $this->render(
                'FrontendBundle:Foro:list.xls.twig', array('entities' => $foros)
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export-foros.xls"');
        return $response;
    }
    
    /**
     * Seleccionar tipo acceso del foro.
     *
     * @Route("/seleccionar/nivel", name="foros_select_nivel")
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
            return $this->redirect($this->generateUrl('foros_new'));
        }
        
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        
        $arreglo = array(
            array('id'=> Foro::TIPO_ACCESO_RESIDENCIAL,'nombre'=>'Residencial'),
            array('id'=> Foro::TIPO_ACCESO_EDIFICIO,'nombre'=>'Por edificio')
        );
        
        return array(
            'entities'=>$arreglo,
            'residencial'=>$residencialActual,
            'ruta' => 'foros_select_nivel',
            'campo' => 'nivel_aviso',
            'titulo' => 'Seleccionar nivel del foro',
            'return' => 'foros',
        );
        
    }
    
    /**
     * @Route("/foro/{id}", name="foros_foro")
     * @Template("FrontendBundle:Foro:foro.html.twig")
     * @Method({"GET","POST"})
     */
    public function foroAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $foro = $em->getRepository('FrontendBundle:Foro')->find($id);
        $comentario = new Comentario();
        $comentario->setForo($foro);
        $comentario->setUsuario($this->getUser());
        $comentario->setNivel(0);
        $form = $this->createForm(new ComentarioType(), $comentario, array('em' => $em));
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($comentario);
                $foro->setContComentarios($foro->getContComentarios() + 1);
                $em->flush();
                if($this->get('security.context')->isGranted('ROLE_ADMIN')){
                    return $this->redirect($this->generateUrl('foros_show',array('id'=>$foro->getId())));
                }
                $comentario = new Comentario();
                $comentario->setForo($foro);
                $comentario->setUsuario($this->getUser());
                $comentario->setNivel(0);
                $form = $this->createForm(new ComentarioType(), $comentario, array('em' => $em));
                $foro = $em->getRepository('FrontendBundle:Foro')->find($id);
            }
        }
        if ($request->isXmlHttpRequest()) {
            $html = $this->renderView('FrontendBundle:Comentario:item.html.twig', array('comentario' => $comentario));
            $response = new JsonResponse(array(
                        'form' => $this->renderView('FrontendBundle:Comentario:formComentario.html.twig', array(
                            'rutaAction' => $this->generateUrl('foros_foro',array('id'=>$foro->getId())),
                            'form' => $form->createView(),
                        )),
                        'respuesta' => 'creado',
                        'html' => $html,
            ));
            return $response;
        }
        $comentarios = $em->getRepository('FrontendBundle:Comentario')
                          ->findBy(array('foro' => $foro), array('createdAt' => 'ASC'));
        return array(
            'foro'=>$foro,
            'comentarios'=>$comentarios,
            'form'=>$form->createView(),
        );
    }
    
    /**
     * Formulario para crear foro.
     *
     * @Route("/crear/foro", name="foros_crear_foro")
     * @Method({"GET","POST"})
     */
    public function crearForoAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUsuarioActual();
        $entity = new Foro();
        $entity->setUsuario($usuario);
        $entity->setTipoAcceso(Foro::TIPO_ACCESO_EDIFICIO);
        $entity->addEdificio($usuario->getEdificio());
        $entity->setResidencial($this->getResidencialActual($this->getResidencialDefault()));
        $entity->setIsCerrado(true);
        $form = $this->createForm(new ForoFrontendType(), $entity, array(
            'action' => $this->generateUrl('foros_crear_foro'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
        ));
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entity = $form->getData();
                $entity->setIsCerrado(false);
                $em->persist($entity);
                $em->flush();
                $response = new JsonResponse(json_encode(array(
                    'json' => json_encode(array(
                        'id'=>$entity->getId(),
                        'titulo'=>$entity->getTitulo(),
                        'comentario'=>$entity->getComentario(),
                    )),
                    'respuesta' => 'creado',
                )));
                return $response;
            }
        }

        $response = new JsonResponse(json_encode(array(
            'form' => $this->renderView('FrontendBundle:Foro:formForo.html.twig', array(
                'rutaAction' => $this->generateUrl('foros_crear_foro'),
                'form' => $form->createView(),
            )),
            'respuesta' => 'nuevo',
        )));
        return $response;
    }
}
