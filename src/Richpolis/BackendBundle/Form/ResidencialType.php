<?php

namespace Richpolis\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Entity\Residencial;

class ResidencialType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre','text',array('attr'=>array('class'=>'form-control')))
            ->add('morosidad',null,array('label'=>'Morosidad','attr'=>array('class'=>'form-control')))
            ->add('tipoMorosidad','choice',array(
                'label'=>'Tipo morosidad',
                'empty_value'=>false,
                'read_only'=> true,
                'choices'=> Residencial::getArrayTipoMorosidad(),
                'preferred_choices'=> Residencial::getPreferedTipoMorosidad(),
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Tipo morosidad',
                    'data-bind'=>'value: tipoMorosidad'
                )))    

        ;   
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\BackendBundle\Entity\Residencial'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'richpolis_backendbundle_residencial';
    }
}
