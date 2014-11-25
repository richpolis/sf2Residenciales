<?php

namespace Richpolis\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\FrontendBundle\Entity\EstadoCuenta;

class CargoAResidencialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('cargo',null,array('attr'=>array('class'=>'form-control')))    
            ->add('monto','money',array('label'=>'Total del cargo','currency'=>'MXN','attr'=>array('class'=>'form-control')))
            ->add('tipoCargo','choice',array(
                'label'=>'Tipo de cargo',
                'empty_value'=>false,
                'read_only'=> true,
                'choices'=>  EstadoCuenta::getArrayTipoCargo(),
                'preferred_choices'=>  EstadoCuenta::getPreferedTipoCargo(),
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Tipo de cargo',
                    'data-bind'=>'value: tipoCargo'
                )))
            ->add('isAcumulable','checkbox',array('label'=>'Acumulable o morosidad?','attr'=>array('class'=>'form-control')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\FrontendBundle\Entity\EstadoCuenta'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_frontendbundle_estadocuenta';
    }
}
