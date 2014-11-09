<?php

namespace Richpolis\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Form\DataTransformer\ResidencialToNumberTransformer;

class AvisoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $residencialTransformer = new ResidencialToNumberTransformer($em);
        
        $builder
            ->add('titulo',null,array('attr'=>array('class'=>'form-control')))
            ->add('aviso',null,array(
                'label'=>'Descripcion',
                'required'=>true,
                'attr'=>array(
                    'class'=>'cleditor tinymce form-control placeholder',
                   'data-theme' => 'advanced',
                    )
                ))
            ->add('tipoAcceso','choice',array(
                'label'=>'Nivel',
                'empty_value'=>false,
                'read_only'=> false,
                'choices'=>  array('1'=>'Residencial','2'=>'Edificio','3'=>'Particular'),
                'preferred_choices'=> array('3'),
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Nivel',
                    'data-bind'=>'value: nivel'
                )))
            ->add('link',null,array('attr'=>array('class'=>'form-control')))
            ->add($builder->create('residencial', 'hidden')->addModelTransformer($residencialTransformer))
            //falta agregar edificio, porque no siempre es a nivel usuario.    
            ->add('usuario','entity',array( 
                    'label' => 'Usuario',
                    'required' => false,
                    'empty_value'=>'Para usuario',
                    'expanded' => false,
                    'class' => 'Richpolis\BackendBundle\Entity\Usuario',
                    'property' => 'nombre',
                    'multiple' => false,
                    'query_builder' => function(\Richpolis\BackendBundle\Repository\UsuarioRepository $er)  {
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
            'data_class' => 'Richpolis\FrontendBundle\Entity\Aviso'
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
        return 'richpolis_frontendbundle_aviso';
    }
}
