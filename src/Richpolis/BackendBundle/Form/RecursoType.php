<?php

namespace Richpolis\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecursoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre','text',array('attr'=>array('class'=>'form-control')))
            ->add('tipoAcceso','checkbox',array('attr'=>array('class'=>'form-control')))
            ->add('precio','money',array('attr'=>array('class'=>'form-control')))
            ->add('edificio',null,array('attr'=>array('class'=>'form-control')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\BackendBundle\Entity\Recurso'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_backendbundle_recurso';
    }
}
