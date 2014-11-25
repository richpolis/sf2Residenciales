<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\FrontendBundle\Entity\Documento;
use Richpolis\FrontendBundle\Form\DocumentoType;
use Richpolis\FrontendBundle\Form\DocumentoPorEdificioType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Documento controller.
 *
 * @Route("/documentos")
 */
class DocumentoController extends BaseController
{

    /**
     * Lists all Documento entities.
     *
     * @Route("/", name="documentos")
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
        $edificio = $this->getEdificioActual();
        $documentos = $em->getRepository('FrontendBundle:Documento')
                     ->findDocumentosPorEdificio($edificio);
        return $this->render("FrontendBundle:Documento:index.html.twig", array(
            'entities' => $documentos,
            'edificio' => $edificio,
            'residencial' => $residencialActual,
        ));
    }
    
    public function usuariosIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $documentos = $em->getRepository('FrontendBundle:Documento')
                     ->findDocumentosPorUsuario($this->getUser());
        return $this->render("FrontendBundle:Documento:documentos.html.twig", array(
            'entities' => $documentos,
            'edificio' => $edificio,
            'residencial' => $residencialActual,
        ));
    }
    /**
     * Creates a new Documento entity.
     *
     * @Route("/", name="documentos_create")
     * @Method("POST")
     * @Template("FrontendBundle:Documento:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Documento();
        $filtros = $this->getFilters();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $entity->setTipoAcceso($filtros['nivel_aviso']);
        $entity->setResidencial($residencial);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('documentos_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Creates a form to create a Documento entity.
     *
     * @param Documento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Documento $entity)
    {
        if($entity->getTipoAcceso() == Documento::TIPO_ACCESO_EDIFICIO){
            $formType = new DocumentoPorEdificioType($entity->getResidencial());
        }else{
            $formType = new DocumentoType();
        }
        $form = $this->createForm($formType, $entity, array(
            'action' => $this->generateUrl('documentos_create'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Documento entity.
     *
     * @Route("/new", name="documentos_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Documento();
        $filtros = $this->getFilters();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $entity->setTipoAcceso($filtros['nivel_aviso']);
        $entity->setResidencial($residencial);
        $entity->addEdificio($edificio);
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Finds and displays a Documento entity.
     *
     * @Route("/{id}", name="documentos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Documento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Documento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Documento entity.
     *
     * @Route("/{id}/edit", name="documentos_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Documento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Documento entity.');
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
    * Creates a form to edit a Documento entity.
    *
    * @param Documento $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Documento $entity)
    {
        if($entity->getTipoAcceso() == Documento::TIPO_ACCESO_EDIFICIO){
            $formType = new DocumentoPorEdificioType($entity->getResidencial());
        }else{
            $formType = new DocumentoType();
        }
        $form = $this->createForm($formType, $entity, array(
            'action' => $this->generateUrl('documentos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Documento entity.
     *
     * @Route("/{id}", name="documentos_update")
     * @Method("PUT")
     * @Template("FrontendBundle:Documento:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Documento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Documento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('documentos_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm)
        );
    }
    /**
     * Deletes a Documento entity.
     *
     * @Route("/{id}", name="documentos_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FrontendBundle:Documento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Documento entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('documentos'));
    }

    /**
     * Creates a form to delete a Documento entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('documentos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Seleccionar tipo acceso del documento.
     *
     * @Route("/seleccionar/nivel", name="documentos_select_nivel")
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
            return $this->redirect($this->generateUrl('avisos_new'));
        }
        
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        
        $arreglo = array(
            array('id'=> Documento::TIPO_ACCESO_RESIDENCIAL,'nombre'=>'Residencial'),
            array('id'=> Documento::TIPO_ACCESO_EDIFICIO,'nombre'=>'Por edificio')
        );
        
        return array(
            'entities'=>$arreglo,
            'residencial'=>$residencialActual,
            'ruta' => 'documentos_select_nivel',
            'campo' => 'nivel_aviso',
            'titulo' => 'Seleccionar nivel del documento',
            'return' => 'documentos',
        );
        
    }
}
