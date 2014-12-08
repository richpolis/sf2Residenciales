<?php

namespace Richpolis\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Form\DataTransformer\EdificiosToArrayTransformer;
use Richpolis\BackendBundle\Entity\Recurso;
use Richpolis\BackendBundle\Entity\Residencial;


class RecursoPorEdificioType extends AbstractType
{
    private $residencial; 
    
    public function __construct(Residencial $residencial) {
        $this->residencial = $residencial;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        
        
        $builder
            ->add('nombre','text',array('attr'=>array('class'=>'form-control')))
            ->add('tipoAcceso','hidden')
            ->add('precio','money',array('label'=>'Cuota por evento','currency'=>'MXN','attr'=>array('class'=>'form-control')))
            ->add('lunes',null,array('label'=>'Lunes','attr'=>array('class'=>'checkbox-inline')))
            ->add('martes',null,array('label'=>'Martes','attr'=>array('class'=>'checkbox-inline')))
            ->add('miercoles',null,array('label'=>'Miercoles','attr'=>array('class'=>'checkbox-inline')))
            ->add('jueves',null,array('label'=>'Jueves','attr'=>array('class'=>'checkbox-inline')))
            ->add('viernes',null,array('label'=>'Viernes','attr'=>array('class'=>'checkbox-inline')))
            ->add('sabado',null,array('label'=>'Sabado','attr'=>array('class'=>'checkbox-inline')))
            ->add('domingo',null,array('label'=>'Domingo','attr'=>array('class'=>'checkbox-inline')))
            ->add('desde','time',array(
                'widget' => 'single_text',
                'with_seconds'=> false,
                'attr'=>array('class'=>'form-control')
                ))
            ->add('hasta','time',array(
                'widget' => 'single_text',
                'with_seconds'=> false,
                'attr'=>array('class'=>'form-control')
                ))
            ->add('edificios','entity',array(
                'class'=>'BackendBundle:Edificio',
                'choices' => $this->residencial->getEdificios(),
                'label'=>'Edificios',
                'expanded' => false,
                'multiple' => true,
                'required' => true,
                'attr'=>array('class'=>'form-control')
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\BackendBundle\Entity\Recurso'
        ))
        ->setRequired(array('em'))
        ->setAllowedTypes(array('em'=>'Doctrine\Common\Persistence\ObjectManager'))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_backendbundle_recurso';
    }
}
