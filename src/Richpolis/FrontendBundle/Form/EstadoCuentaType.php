<?php

namespace Richpolis\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class EstadoCuentaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $residencial = $options['residencial'];
        
        $builder
            ->add('cargo',null,array('attr'=>array('class'=>'form-control')))
            ->add('tipo','choice',array(
                'label'=>'Tipo de cargo',
                'empty_value'=>false,
                'read_only'=> false,
                'choices'=>  array('1'=>'Cargo normal','2'=>'Adeudo','3'=>'Intereses'),
                'preferred_choices'=> array('1'),
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Tipo de cargo',
                    'data-bind'=>'value: tipo'
                )))
            ->add('usuario','entity',array( 
                    'label' => 'Usuario',
                    'required' => true,
                    'expanded' => false,
                    'class' => 'Richpolis\BackendBundle\Entity\Usuario',
                    'property' => 'nombre',
                    'multiple' => false,
                    'query_builder' => function(\Richpolis\BackendBundle\Repository\UsuariosRepository $er)  {
                        return $er->queryUsuariosResidencial($residencial);
                    },                
                    'attr'=>array(
                        'class'=>'validate[required] form-control placeholder',
                        'placeholder'=>'Usuario',
                        'data-bind'=>'value: usuario',
                    )
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\FrontendBundle\Entity\EstadoCuenta'
        ))
        ->setRequired(array('residencial'))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_frontendbundle_estadocuenta';
    }
}
