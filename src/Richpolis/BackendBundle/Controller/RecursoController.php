<?php

namespace Richpolis\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\BackendBundle\Entity\Recurso;
use Richpolis\BackendBundle\Form\RecursoType;
use Richpolis\BackendBundle\Form\RecursoPorEdificioType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Recurso controller.
 *
 * @Route("/recursos")
 */
class RecursoController extends BaseController
{

    /**
     * Lists all Recurso entities.
     *
     * @Route("/", name="recursos")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('BackendBundle:Recurso')->findAll();
        
        if($request->query->has('edificio')){
            $filters = $this->getFilters();
            $filters['edificio'] = $request->query->get('edificio');
            $this->setFilters($filters);
        }
        
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificioActual = $this->getEdificioActual();
        
        $recursos = $em->getRepository('BackendBundle:Recurso')
			->getRecursosPorEdificio($edificioActual->getId(),$residencialActual->getId());

        return array(
            'entities'      => $recursos,
            'residencial'   => $residencialActual,
            'edificio'      => $edificioActual,
        );
    }
    /**
     * Creates a new Recurso entity.
     *
     * @Route("/", name="recursos_create")
     * @Method("POST")
     * @Template("BackendBundle:Recurso:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Recurso();
        $filtros = $this->getFilters();
        $entity->setTipoAcceso($filtros['nivel_aviso']);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('recursos_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
			'edificio'=> $this->getEdificioActual(),
        );
    }

    /**
     * Creates a form to create a Recurso entity.
     *
     * @param Recurso $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Recurso $entity)
    {
        if($entity->getTipoAcceso() == Recurso::TIPO_ACCESO_EDIFICIO){
            $formType = new RecursoPorEdificioType($this->getResidencialActual($this->getResidencialDefault()));
        }else{
            $formType = new RecursoType();
        }
        $form = $this->createForm($formType, $entity, array(
            'action' => $this->generateUrl('recursos_create'),
            'method' => 'POST',
            'em'=>$this->getDoctrine()->getManager(),
        ));

        ////$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Recurso entity.
     *
     * @Route("/new", name="recursos_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Recurso();
        $filtros = $this->getFilters();
        $edificio = $this->getEdificioActual();
        $entity->setTipoAcceso($filtros['nivel_aviso']);
        $entity->addEdificio($edificio);
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
			'edificio' => $edificio,
        );
    }

    /**
     * Finds and displays a Recurso entity.
     *
     * @Route("/{id}", name="recursos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Recurso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recurso entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
			'edificio'=> $this->getEdificioActual(),
        );
    }

    /**
     * Displays a form to edit an existing Recurso entity.
     *
     * @Route("/{id}/edit", name="recursos_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Recurso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recurso entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
			'edificio'=> $this->getEdificioActual(),
        );
    }

    /**
    * Creates a form to edit a Recurso entity.
    *
    * @param Recurso $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Recurso $entity)
    {
        if($entity->getTipoAcceso() == Recurso::TIPO_ACCESO_EDIFICIO){
            $formType = new RecursoPorEdificioType($this->getResidencialActual($this->getResidencialDefault()));
        }else{
            $formType = new RecursoType();
        }
        $form = $this->createForm($formType, $entity, array(
            'action' => $this->generateUrl('recursos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em'=>$this->getDoctrine()->getManager(),
        ));

        ////$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Recurso entity.
     *
     * @Route("/{id}", name="recursos_update")
     * @Method("PUT")
     * @Template("BackendBundle:Recurso:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Recurso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recurso entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('recursos_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
			'edificio'=> $this->getEdificioActual(),
        );
    }
    /**
     * Deletes a Recurso entity.
     *
     * @Route("/{id}", name="recursos_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Recurso')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Recurso entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('recursos'));
    }

    /**
     * Creates a form to delete a Recurso entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('recursos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ////->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Seleccionar tipo acceso del recurso.
     *
     * @Route("/seleccionar/nivel", name="recursos_select_nivel")
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
                case Recurso::TIPO_ACCESO_RESIDENCIAL:
                case Recurso::TIPO_ACCESO_EDIFICIO:
				case Recurso::TIPO_ACCESO_PRIVADO:
                    return $this->redirect($this->generateUrl('recursos_new'));
            }
        }
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $arreglo = array(
            array('id'=>  Recurso::TIPO_ACCESO_RESIDENCIAL,'nombre'=>'Para Residencial'),
            array('id'=>  Recurso::TIPO_ACCESO_EDIFICIO,'nombre'=>'Por torre'),
            /*array('id'=>  Recurso::TIPO_ACCESO_PRIVADO,'nombre'=>'Privado (solo administración)'),*/
        );
        return array(
            'entities'=>$arreglo,
            'residencial'=>$residencialActual,
            'ruta' => 'recursos_select_nivel',
            'campo' => 'nivel_aviso',
            'titulo' => 'Seleccionar nivel del recurso',
            'return' => 'recursos',
        );
    }
}
