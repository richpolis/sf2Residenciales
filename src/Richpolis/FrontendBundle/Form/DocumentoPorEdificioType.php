<?php

namespace Richpolis\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Form\DataTransformer\ResidencialToNumberTransformer;
use Richpolis\BackendBundle\Entity\Residencial;

class DocumentoPorEdificioType extends AbstractType
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
        
        $builder
            ->add('titulo',null,array('attr'=>array('class'=>'form-control')))
            ->add('descripcion',null,array(
                'label'=>'Descripcion',
                'required'=>true,
                'attr'=>array(
                    'class'=>'cleditor tinymce form-control placeholder',
                   'data-theme' => 'advanced',
                    )
                ))
            ->add('file','file',array('label'=>'Archivo','attr'=>array('class'=>'form-control')))
            ->add('archivo','hidden')
            ->add('tipoArchivo','hidden')
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
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\FrontendBundle\Entity\Documento'
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
        return 'richpolis_frontendbundle_documento';
    }
}
