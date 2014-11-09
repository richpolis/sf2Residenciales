<?php

namespace Richpolis\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo',null,array('attr'=>array('class'=>'form-control')))
            ->add('descripcion',null,array('attr'=>array('class'=>'form-control')))
            ->add('file','file',array('label'=>'Archivo','attr'=>array('class'=>'form-control')))
            ->add('archivo','hidden')
            ->add('tipoArchivo',null,array('attr'=>array('class'=>'form-control')))
            ->add('residencial')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\FrontendBundle\Entity\Documento'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_frontendbundle_documento';
    }
}
