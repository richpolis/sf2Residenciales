<?php

namespace Richpolis\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Form\DataTransformer\EdificioToNumberTransformer;

class RecursoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $edificioTransformer = new EdificioToNumberTransformer($em);
        
        
        $builder
            ->add('nombre','text',array('attr'=>array('class'=>'form-control')))
            ->add('tipoAcceso','checkbox',array('attr'=>array('class'=>'form-control')))
            ->add('precio','money',array('attr'=>array('class'=>'form-control')))
            ->add($builder->create('edificio', 'hidden')->addModelTransformer($edificioTransformer))
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
