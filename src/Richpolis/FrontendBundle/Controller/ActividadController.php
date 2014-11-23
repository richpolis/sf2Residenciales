<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Richpolis\BackendBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\FrontendBundle\Entity\Actividad;
use Richpolis\FrontendBundle\Form\ActividadType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Actividad controller.
 *
 * @Route("/actividades")
 */
class ActividadController extends BaseController
{

    /**
     * Lists all Actividad entities.
     *
     * @Route("/", name="actividades")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('FrontendBundle:Actividad')->findAll();
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        
        $actividades = $em->getRepository('FrontendBundle:Actividad')
                          ->findBy(array('residencial'=>$residencialActual));

        if($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $pagina = 'index';
        }else{
            $pagina = 'actividades';
        }
        return $this->render("FrontendBundle:Actividad:$pagina.html.twig", array(
                'entities' => $actividades,
                'residencial' => $residencialActual,
        ));
    }
    
    /**
     * Creates a new Actividad entity.
     *
     * @Route("/", name="actividades_create")
     * @Method("POST")
     * @Template("FrontendBundle:Actividad:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $entity = new Actividad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if($entity->getTipoAcceso()==Actividad::TIPO_ACCESO_EDIFICIO){
            $form->add('edificios','entity',array(
                'class'=>'BackendBundle:Edificio',
                'choices' => $residencial->getEdificios(),
                'label'=>'Edificios',
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'attr'=>array('class'=>'form-control')
                ));    
        }
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('actividades_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Creates a form to create a Actividad entity.
     *
     * @param Actividad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Actividad $entity)
    {
        $form = $this->createForm(new ActividadType(), $entity, array(
            'action' => $this->generateUrl('actividades_create'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Actividad entity.
     *
     * @Route("/new", name="actividades_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Actividad();
        $filtros = $this->getFilters();
        $residencial = $this->getResidencialActual($this->getResidencialDefault());
        $edificio = $this->getEdificioActual();
        $entity->setTipoAcceso($filtros['nivel_aviso']);
        $entity->setResidencial($residencial);
        $form   = $this->createCreateForm($entity);
        if($filtros['nivel_aviso']==Actividad::TIPO_ACCESO_EDIFICIO){
            $form->add('edificios','entity',array(
                'class'=>'BackendBundle:Edificio',
                'choices' => $residencial->getEdificios(),
                'label'=>'Edificios',
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'attr'=>array('class'=>'form-control')
                ));    
        }           
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form),
        );
    }

    /**
     * Finds and displays a Actividad entity.
     *
     * @Route("/{id}", name="actividades_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Actividad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actividad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Actividad entity.
     *
     * @Route("/{id}/edit", name="actividades_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Actividad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actividad entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        
        if($entity->getTipoAcceso()==Actividad::TIPO_ACCESO_EDIFICIO){
            $editForm->add('edificios','entity',array(
                'class'=>'BackendBundle:Edificio',
                'choices' => $residencial->getEdificios(),
                'label'=>'Edificios',
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'attr'=>array('class'=>'form-control')
                ));    
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
        );
    }

    /**
    * Creates a form to edit a Actividad entity.
    *
    * @param Actividad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Actividad $entity)
    {
        $form = $this->createForm(new ActividadType(), $entity, array(
            'action' => $this->generateUrl('actividades_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em' => $this->getDoctrine()->getManager(),
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * Edits an existing Actividad entity.
     *
     * @Route("/{id}", name="actividades_update")
     * @Method("PUT")
     * @Template("FrontendBundle:Actividad:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FrontendBundle:Actividad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actividad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if($entity->getTipoAcceso()==Actividad::TIPO_ACCESO_EDIFICIO){
            $editForm->add('edificios','entity',array(
                'class'=>'BackendBundle:Edificio',
                'choices' => $residencial->getEdificios(),
                'label'=>'Edificios',
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'attr'=>array('class'=>'form-control')
                ));    
        }
        
        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('actividades_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm),
        );
    }
    
    /**
     * Deletes a Actividad entity.
     *
     * @Route("/{id}", name="actividades_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FrontendBundle:Actividad')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Actividad entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('actividades'));
    }

    /**
     * Creates a form to delete a Actividad entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('actividades_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Seleccionar tipo acceso de la actividad.
     *
     * @Route("/seleccionar/nivel", name="actividades_select_nivel")
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
                case Actividad::TIPO_ACCESO_RESIDENCIAL:
                case Actividad::TIPO_ACCESO_EDIFICIO:
                    return $this->redirect($this->generateUrl('actividades_new'));
                case Actividad::TIPO_ACCESO_PRIVADO:
                    return $this->redirect($this->generateUrl('actividades_select_usuario'));
            }
        }
        
        $residencialActual = $this->getResidencialActual($this->getResidencialDefault());
        
        $arreglo = array(
            array('id'=>  Actividad::TIPO_ACCESO_RESIDENCIAL,'nombre'=>'Para Residencial'),
            array('id'=>  Actividad::TIPO_ACCESO_EDIFICIO,'nombre'=>'Por torre'),
        );
        
        return array(
            'entities'=>$arreglo,
            'residencial'=>$residencialActual,
            'ruta' => 'actividades_select_nivel',
            'campo' => 'nivel_aviso',
            'titulo' => 'Seleccionar nivel de la actividad',
            'return' => 'actividades',
        );
        
    }
}
