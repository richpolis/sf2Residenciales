<?php

namespace Richpolis\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Form\DataTransformer\ResidencialToNumberTransformer;
use Richpolis\BackendBundle\Form\DataTransformer\UsuarioToNumberTransformer;
use Richpolis\BackendBundle\Entity\Residencial;
use Richpolis\FrontendBundle\Entity\Aviso;

class AvisoPorEdificioType extends AbstractType
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
        $residencialTransformer = new ResidencialToNumberTransformer($em);
        $usuarioTransformer = new UsuarioToNumberTransformer($em);
        
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
            ->add('tipoAcceso','hidden')
            ->add('tipoAviso','choice',array(
                'label'=>'Tipo de aviso',
                'empty_value'=>false,
                'read_only'=> true,
                'choices'=>  Aviso::getArrayTipoAviso(),
                'preferred_choices'=>  Aviso::getPreferedTipoAviso(),
                'attr'=>array(
                    'class'=>'validate[required] form-control placeholder',
                    'placeholder'=>'Tipo de aviso',
                    'data-bind'=>'value: tipoAviso'
                )))
            ->add('enviarEmail',null,array('label'=>'Enviar email?','attr'=>array(
                'class'=>'checkbox-inline',
                'data-bind'=>'value: enviarEmail'
             )))
            ->add('link','hidden')
            ->add('edificios','entity',array(
                'class'=>'BackendBundle:Edificio',
                'choices' => $this->residencial->getEdificios(),
                'label'=>'Torres',
                'expanded' => false,
                'multiple' => true,
                'required' => true,
                'attr'=>array('class'=>'form-control')
                ))    
            ->add($builder->create('residencial', 'hidden')->addModelTransformer($residencialTransformer))
            ->add($builder->create('usuario', 'hidden')->addModelTransformer($usuarioTransformer))    
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
        ->setRequired(array('em',))
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
