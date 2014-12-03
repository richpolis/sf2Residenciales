<?php

namespace Richpolis\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Form\DataTransformer\UsuarioToNumberTransformer;
use Richpolis\BackendBundle\Form\DataTransformer\ResidencialToNumberTransformer;
use Richpolis\BackendBundle\Entity\Residencial;

class ForoPorEdificioType extends AbstractType
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
        $usuarioTransformer = new UsuarioToNumberTransformer($em);
        $residencialTransformer = new ResidencialToNumberTransformer($em);
        $builder
            ->add('titulo',null,array('attr'=>array('class'=>'form-control'))) 
            ->add('comentario',null,array(
                'label'=>'Descripcion',
                'required'=>true,
                'attr'=>array(
                    'class'=>'cleditor tinymce form-control placeholder',
                   'data-theme' => 'advanced',
                    )
                ))
            ->add('tipoDiscusion','hidden')
            ->add('tipoAcceso','hidden')
            ->add('isCerrado',null,array('label'=>'Es cerrado?'))
            ->add($builder->create('residencial', 'hidden')->addModelTransformer($residencialTransformer))
            ->add('edificios','entity',array(
                'class'=>'BackendBundle:Edificio',
                'choices' => $this->residencial->getEdificios(),
                'label'=>'Torres',
                'expanded' => false,
                'multiple' => true,
                'required' => true,
                'attr'=>array('class'=>'form-control')
                ))
            ->add($builder->create('usuario', 'hidden')->addModelTransformer($usuarioTransformer))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\FrontendBundle\Entity\Foro'
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
        return 'richpolis_frontendbundle_foro';
    }
}
