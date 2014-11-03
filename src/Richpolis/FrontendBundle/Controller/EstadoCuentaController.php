<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\FrontendBundle\Entity\EstadoCuenta;
use Richpolis\FrontendBundle\Form\EstadoCuentaType;

/**
 * EstadoCuenta controller.
 *
 * @Route("/estadodecuentas")
 */
class EstadoCuentaController extends Controller
{

    /**
     * Lists all EstadoCuenta entities.
     *
     * @Route("/", name="estadodecuentas")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FrontendBundle:EstadoCuenta')->findAll();

        return array(
            'entities' => $entities,
        );
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
    public function newAction()
    {
        $entity = new EstadoCuenta();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a EstadoCuenta entity.
     *
     * @Route("/{id}", name="estadodecuentas_show")
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
        $form = $this->createForm(new EstadoCuentaType(), $entity, array(
            'action' => $this->generateUrl('estadodecuentas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
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
}
