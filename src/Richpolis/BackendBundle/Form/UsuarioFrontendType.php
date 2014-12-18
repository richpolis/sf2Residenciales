<?php

namespace Richpolis\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Form\DataTransformer\EdificioToNumberTransformer;

class UsuarioFrontendType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        //$is_super_admin = $options['is_super_admin'];
        $edificioTransformer = new EdificioToNumberTransformer($em);
        
        $builder
            ->add('email','email',array('attr'=>array('class'=>'form-control')))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Las dos contraseñas deben coincidir',
                'first_options'   => array('label' => 'Contraseña'),
                'second_options'  => array('label' => 'Repite Contraseña'),
                'required'        => false,
                'options' => array(
                    'attr'=>array('class'=>'form-control placeholder')
                )
            )) 
            ->add('salt','hidden')
            ->add('nombre','text',array('attr'=>array('class'=>'form-control')))
            ->add('telefono','text',array('attr'=>array('class'=>'form-control')))
            ->add('imagen','hidden')
            ->add('numero','text',array(
                'label'=>'Departamento',
                'read_only'=>true,
                'attr'=>array('class'=>'form-control')
                ))
            ->add($builder->create('edificio', 'hidden')->addModelTransformer($edificioTransformer))
            ->add('isActive','hidden')
            ->add('grupo','hidden')    
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\BackendBundle\Entity\Usuario'
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
        return 'richpolis_backendbundle_usuario';
    }
}
