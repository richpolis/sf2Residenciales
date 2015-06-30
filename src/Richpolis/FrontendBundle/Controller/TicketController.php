<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\FrontendBundle\Entity\Foro;
use Richpolis\FrontendBundle\Form\TicketType;
use Richpolis\FrontendBundle\Form\ComentarioType;
use Richpolis\FrontendBundle\Entity\Comentario;


use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Foro controller.
 *
 * @Route("/tickets")
 */
class TicketController extends BaseController
{

    /**
     * Lists all Foro entities.
     *
     * @Route("/", name="tickets")
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
                ->queryFindTicketsPorEdificio($edificioActual, $buscar);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1), 10, $options
        );
        return $this->render("FrontendBundle:Ticket:index.html.twig", array(
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

        $tickets = $em->getRepository('FrontendBundle:Foro')
                ->findTicketsPorUsuarioPorFecha($this->getUser(),$month,$year);
        
        return $this->render("FrontendBundle:Ticket:tickets.html.twig", array(
            'entities' => $tickets,
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
     * @Route("/", name="tickets_create")
     * @Method("POST")
     * @Template("FrontendBundle:Ticket:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Foro();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $entity->setResidencial($residencial);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $entity->setTitulo('T'.$entity->getId());
            $em->flush();
            return $this->redirect($this->generateUrl('tickets_show', array('id' => $entity->getId())));
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
        $form = $this->createForm(new TicketType(), $entity, array(
            'action' => $this->generateUrl('tickets_create'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Foro entity.
     *
     * @Route("/new", name="tickets_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $entity = new Foro();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $entity->setTipoAcceso(Foro::TIPO_ACCESO_PRIVADO);
        $entity->setResidencial($residencial);
        $entity->addEdificio($edificio);
        $entity->setUsuario($this->getUser());
        $entity->setTitulo('Ticket nuevo');
        $form   = $this->createCreateForm($entity);
        
        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse(json_encode(array(
            'form' => $this->renderView('FrontendBundle:Ticket:formForo.html.twig', array(
                'rutaAction' => $this->generateUrl('tickets_crear_ticket'),
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
     * @Route("/{id}", name="tickets_show")
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
     * @Route("/{id}/edit", name="tickets_edit")
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
        $form = $this->createForm(new TicketType(), $entity, array(
            'action' => $this->generateUrl('tickets_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Foro entity.
     *
     * @Route("/{id}", name="tickets_update")
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

            return $this->redirect($this->generateUrl('tickets_edit', array('id' => $id)));
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
     * @Route("/{id}", name="tickets_delete")
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

        return $this->redirect($this->generateUrl('tickets'));
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
            ->setAction($this->generateUrl('tickets_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Exportar los tickets.
     *
     * @Route("/exportar", name="tickets_exportar")
     */
    public function exportarAction(Request $request)
    {
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        
        $tickets = $this->getDoctrine()
                ->getRepository('FrontendBundle:Pago')
                ->findTicketsPorEdificio("",$edificio);

        $response = $this->render(
                'FrontendBundle:Ticket:list.xls.twig', array('entities' => $tickets)
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export-tickets.xls"');
        return $response;
    }
    
    /**
     * Seleccionar usuario del ticket.
     *
     * @Route("/seleccionar/usuario", name="tickets_select_usuario")
     * @Template("FrontendBundle:Reservacion:selectUsuario.html.twig")
     */
    public function selectUsuarioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        if($request->query->has('usuario')){
            $filtros = $this->getFilters();
            $filtros['usuario'] = $request->query->get('usuario');
            $this->setFilters($filtros);
            return $this->redirect($this->generateUrl('tickets_new'));
        }
        
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $usuarios = $em->getRepository('BackendBundle:Usuario')
                       ->findBy(array('edificio'=>$edificio));
        return array(
            'entities'=>$usuarios,
            'residencial'=>$residencialActual,
            'edificio'=> $edificio,
            'ruta' => 'tickets_select_usuario',
            'campo' => 'usuario',
            'titulo' => 'Ticket para usuario: seleccionar usuario',
            'return' =>'tickets'
        );
        
    }
    
    /**
     * @Route("/ticket/{id}", name="tickets_ticket")
     * @Template("FrontendBundle:Ticket:ticket.html.twig")
     * @Method({"GET","POST"})
     */
    public function ticketAction(Request $request, $id) {
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
                $foro->setComentario($this->getUser()->getNombre().": ". $comentario->getComentario());
                $em->flush();
                /*if($this->get('security.context')->isGranted('ROLE_ADMIN')){
                    return $this->redirect($this->generateUrl('tickets_show',array('id'=>$foro->getId())));
                }*/
                $comentarioNuevo = new Comentario();
                $comentarioNuevo->setForo($foro);
                $comentarioNuevo->setUsuario($this->getUser());
                $comentarioNuevo->setNivel(0);
                $form = $this->createForm(new ComentarioType(), $comentarioNuevo, array('em' => $em));
                $foro = $em->getRepository('FrontendBundle:Foro')->find($id);
            }
        }
        if ($request->isXmlHttpRequest()) {
            $html = $this->renderView('FrontendBundle:Comentario:item.html.twig', array('comentario' => $comentario));
            $response = new JsonResponse(array(
                        'form' => $this->renderView('FrontendBundle:Comentario:formComentario.html.twig', array(
                            'rutaAction' => $this->generateUrl('tickets_ticket',array('id'=>$foro->getId())),
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
            'ticket'=>$foro,
            'comentarios'=>$comentarios,
            'form'=>$form->createView(),
        );
    }
    
    /**
     * Formulario para crear foro.
     *
     * @Route("/crear/ticket", name="tickets_crear_ticket")
     * @Method({"GET","POST"})
     */
    public function crearTicketAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUsuarioActual();
        //Crea un comentario inicial, porque es lo que realmente enviara el usuario al administrador.
        $entity = new Comentario();
        $entity->setUsuario($usuario);
        $form = $this->createForm(new ComentarioType(), $entity, array(
            'action' => $this->generateUrl('tickets_crear_ticket'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
        ));
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entity = $form->getData();
                $foro = new Foro();
                $foro->setUsuario($usuario);
                $foro->setTipoAcceso(Foro::TIPO_ACCESO_PRIVADO);
                $foro->addEdificio($usuario->getEdificio());
                $foro->setResidencial($usuario->getEdificio()->getResidencial());
                $foro->setTitulo('Ticket nuevo');
                $foro->setComentario($usuario->getNombre().": ". $entity->getComentario());
                $foro->setContComentarios(1);
                $entity->setForo($foro);
                $em->persist($foro);
                $em->persist($entity);
                $em->flush();
                $foro->setTitulo('T'.$foro->getId());
                $em->flush();
                $response = new JsonResponse(json_encode(array(
                    'json' => json_encode(array(
                        'id'=>$foro->getId(),
                        'titulo'=>$foro->getTitulo(),
                        'comentario'=>$foro->getComentario(),
                    )),
                    'respuesta' => 'creado',
                )));
                return $response;
            }
        }

        $response = new JsonResponse(json_encode(array(
            'form' => $this->renderView('FrontendBundle:Ticket:formTicket.html.twig', array(
                'rutaAction' => $this->generateUrl('tickets_crear_ticket'),
                'form' => $form->createView(),
            )),
            'respuesta' => 'nuevo',
        )));
        return $response;
    }
}
