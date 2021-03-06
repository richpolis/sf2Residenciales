<?php

namespace Richpolis\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\BackendBundle\Entity\Edificio;
use Richpolis\BackendBundle\Form\EdificioType;


use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Edificio controller.
 *
 * @Route("/torres")
 */
class EdificioController extends BaseController
{
    /**
     * Lists all Edificio entities.
     *
     * @Route("/", name="edificios")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('BackendBundle:Edificio')->findAll();
        if(true === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')){
            if($request->query->has('residencial')){
                $filters = $this->getFilters();
                $filters['residencial'] = $request->query->get('residencial');
				unset($filters['edificio']);
                $this->setFilters($filters);
            }
        }
        
        $residenciaActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificios = $em->getRepository('BackendBundle:Edificio')
                        ->findBy(array('residencial'=>$residenciaActual));
        
        return array(
            'entities'      =>  $edificios,
            'residencial'   =>  $residenciaActual,
        );
    }
    
    /**
     * Creates a new Edificio entity.
     *
     * @Route("/", name="edificios_create")
     * @Method("POST")
     * @Template("BackendBundle:Edificio:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Edificio();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('edificios_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Creates a form to create a Edificio entity.
     *
     * @param Edificio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Edificio $entity)
    {
        $form = $this->createForm(new EdificioType(), $entity, array(
            'action' => $this->generateUrl('edificios_create'),
            'method' => 'POST',
            'em'=>$this->getDoctrine()->getManager(),
        ));

        ////$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Edificio entity.
     *
     * @Route("/new", name="edificios_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Edificio();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $entity->setResidencial($residencial);
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Finds and displays a Edificio entity.
     *
     * @Route("/{id}", name="edificios_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Edificio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Edificio entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Edificio entity.
     *
     * @Route("/{id}/edit", name="edificios_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Edificio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Edificio entity.');
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
    * Creates a form to edit a Edificio entity.
    *
    * @param Edificio $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Edificio $entity)
    {
        $form = $this->createForm(new EdificioType(), $entity, array(
            'action' => $this->generateUrl('edificios_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em'=>$this->getDoctrine()->getManager(),
        ));

        ////$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Edificio entity.
     *
     * @Route("/{id}", name="edificios_update")
     * @Method("PUT")
     * @Template("BackendBundle:Edificio:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Edificio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Edificio entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('edificios_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
        );
    }
    /**
     * Deletes a Edificio entity.
     *
     * @Route("/{id}", name="edificios_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Edificio')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Edificio entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('edificios'));
    }

    /**
     * Creates a form to delete a Edificio entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('edificios_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ////->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
