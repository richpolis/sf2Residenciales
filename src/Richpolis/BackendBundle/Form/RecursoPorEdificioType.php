<?php

namespace Richpolis\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Form\DataTransformer\EdificiosToArrayTransformer;
use Richpolis\BackendBundle\Entity\Recurso;

class RecursoType extends AbstractType
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
            ->add('tipoAcceso','choice',array(
                'label'=>'Tipo acceso',
                'empty_value'=>false,
                'read_only'=> true,
                'choices'=> Recurso::getArrayTipoAcceso(),
                'preferred_choices'=> Recurso::getPreferedTipoAcceso(),
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Tipo acceso',
                    'data-bind'=>'value: tipoAcceso'
                )))
            ->add('precio','money',array('label'=>'cuota','currency'=>'MXN','attr'=>array('class'=>'form-control')))
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
