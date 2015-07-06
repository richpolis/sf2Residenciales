<?php

namespace Richpolis\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\BackendBundle\Entity\Residencial;
use Richpolis\BackendBundle\Form\ResidencialType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Residencial controller.
 *
 * @Route("/residenciales")
 */
class ResidencialController extends Controller
{

    /**
     * Lists all Residencial entities.
     *
     * @Route("/", name="residenciales")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        if($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')){
            $entities = $em->getRepository('BackendBundle:Residencial')->findAll();
        }else{
            $entities = $this->getUser()->getResidenciales();
        }

        return array(
            'entities' => $entities,
        );
    }
    
    /**
     * Creates a new Residencial entity.
     *
     * @Route("/", name="residenciales_create")
     * @Method("POST")
     * @Template("BackendBundle:Residencial:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Residencial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('residenciales_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Creates a form to create a Residencial entity.
     *
     * @param Residencial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Residencial $entity)
    {
        $form = $this->createForm(new ResidencialType(), $entity, array(
            'action' => $this->generateUrl('residenciales_create'),
            'method' => 'POST',
        ));

        ////$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Residencial entity.
     *
     * @Route("/new", name="residenciales_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Residencial();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Finds and displays a Residencial entity.
     *
     * @Route("/{id}", name="residenciales_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Residencial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Residencial entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Residencial entity.
     *
     * @Route("/{id}/edit", name="residenciales_edit", requirements={"id": "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Residencial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Residencial entity.');
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
    * Creates a form to edit a Residencial entity.
    *
    * @param Residencial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Residencial $entity)
    {
        $form = $this->createForm(new ResidencialType(), $entity, array(
            'action' => $this->generateUrl('residenciales_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        ////$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Residencial entity.
     *
     * @Route("/{id}", name="residenciales_update", requirements={"id": "\d+"})
     * @Method("PUT")
     * @Template("BackendBundle:Residencial:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Residencial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Residencial entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('residenciales_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
        );
    }
    /**
     * Deletes a Residencial entity.
     *
     * @Route("/{id}", name="residenciales_delete", requirements={"id": "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Residencial')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Residencial entity.');
            }
			
			$edificios= $entity->getEdificios();
			foreach($edificios as $edificio){
				$usuarios = $edificio->getUsuarios();
				foreach($usuarios as $usuario){
					$edificio->removeUsuario($usuario);
					$em->remove($usuario);
				}
				$recursos = $edificio->getRecursos();
				foreach($recursos as $recurso){
					$edificio->removeRecurso($recurso);
					$em->remove($recurso);
				}
				
				$entity->removeEdificio($edificio);
				$em->remove($edificio);
			}

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('residenciales'));
    }

    /**
     * Creates a form to delete a Residencial entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('residenciales_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete','attr'=>array('class'=>'btn btn-danger')))
            ->getForm()
        ;
    }
    
    /**
     * Seleccionar alguna residencial.
     *
     * @Route("/seleccionar", name="residenciales_seleccionar")
     * @Method("GET")
     */
    public function seleccionarAction() {
        $context = $this->get('security.context');
        if (true === $context->isGranted('ROLE_SUPER_ADMIN')) {
            $entities = $this->getDoctrine()->getRepository('BackendBundle:Residencial')->findAll();
        } else if (true === $context->isGranted('ROLE_ADMIN')) {
            $entities = $this->getUser()->getResidenciales();
        } else {
            $entities = array();
        }
        return $this->render('BackendBundle:Residencial:seleccionar.html.twig', array(
            'entities' => $entities,
        ));
    }
}
