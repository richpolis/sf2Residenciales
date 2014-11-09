<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\FrontendBundle\Entity\Reservacion;
use Richpolis\FrontendBundle\Form\ReservacionType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

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
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('FrontendBundle:Reservacion')->findAll();
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();
        
        $reservaciones = $em->getRepository('FrontendBundle:EstadoCuenta')
                        ->getReservacionesPorEdificio($edificioActual->getId());

        return array(
            'entities' => $reservaciones,
            'residencial'=>$residencialActual,
            'edificio'=>$edificioActual,
        );
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
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Reservacion();
        $filtros = $this->getFilters();
        $recurso = $em->find('BackendBundle:Recurso', $filtros['recurso']);
        $usuario = $em->find('BackendBundle:Usuario', $filtros['usuario']);
        $entity->setRecurso($recurso);
        $entity->setUsuario($usuario);
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Finds and displays a Reservacion entity.
     *
     * @Route("/{id}", name="reservaciones_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Reservacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reservacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
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
            'errores' => RpsStms::getErrorMessages($editForm)
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
            'errores' => RpsStms::getErrorMessages($editForm)
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
            'titulo' => 'Seleccionar edificio del usuario',
        );
        
    }
    
    /**
     * Seleccionar recurso para reservacion.
     *
     * @Route("/seleccionar/recurso", name="reservaciones_select_recurso")
     * @Template("FrontendBundle:Reservacion:select.html.twig")
     */
    public function selectRecursoAction()
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
        );
        
    }
    
    /**
     * Seleccionar usuario para reservacion.
     *
     * @Route("/seleccionar/usuario", name="reservaciones_select_usuario")
     * @Template("FrontendBundle:Reservacion:select.html.twig")
     */
    public function selectUsuarioAction()
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
            'titulo' => 'Seleccionar usuario',
        );
        
    }
}