<?php

namespace Richpolis\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Entity\Usuario;
use Richpolis\BackendBundle\Form\DataTransformer\EdificioToNumberTransformer;

class UsuarioType extends AbstractType
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
            ->add('username','text',array('attr'=>array('class'=>'form-control')))
            ->add('password','password',array('attr'=>array('class'=>'form-control')))
            ->add('salt','hidden')
            ->add('nombre','text',array('attr'=>array('class'=>'form-control')))
            ->add('email','email',array('attr'=>array('class'=>'form-control')))
            ->add('telefono','text',array('attr'=>array('class'=>'form-control')))
            ->add('imagen','hidden')
            ->add('numero','text',array('attr'=>array('class'=>'form-control')))
            ->add($builder->create('edificio', 'hidden')->addModelTransformer($edificioTransformer))
            ->add('grupo','choice',array(
                'label'=>'Grupo',
                'empty_value'=>false,
                'choices'=>Usuario::getArrayTipoGrupo(),
                'preferred_choices'=>Usuario::getPreferedTipoGrupo(),
                'attr'=>array(
                    'class'=>'form-control',
                    'placeholder'=>'Grupo',
            )))    
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
