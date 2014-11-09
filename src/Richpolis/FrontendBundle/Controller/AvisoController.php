<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\FrontendBundle\Entity\Aviso;
use Richpolis\FrontendBundle\Form\AvisoType;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('FrontendBundle:Aviso')->findAll();
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        
        $avisos = $em->getRepository('FrontendBundle:Aviso')
                          ->findBy(array('residencial'=>$residencialActual));

        return array(
            'entities' => $avisos,
            'residencial' => $residencialActual,
        );
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
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

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
        $form = $this->createForm(new AvisoType(), $entity, array(
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
        $entity = new Aviso();
        $form   = $this->createCreateForm($entity);

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
        $form = $this->createForm(new AvisoType(), $entity, array(
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
}
