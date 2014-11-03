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
            ->add('titulo')
            ->add('descripcion')
            ->add('archivo')
            ->add('tipoArchivo')
            ->add('createdAt')
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
