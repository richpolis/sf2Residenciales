<?php

namespace Richpolis\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\FrontendBundle\Form\DataTransformer\ComentarioToNumberTransformer;
use Richpolis\FrontendBundle\Form\DataTransformer\ForoToNumberTransformer;
use Richpolis\BackendBundle\Form\DataTransformer\UsuarioToNumberTransformer;

class ComentarioType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
	$parentTransformer = new ComentarioToNumberTransformer($em);
	$foroTransformer = new ForoToNumberTransformer($em);
        $usuarioTransformer = new UsuarioToNumberTransformer($em);
        
        $builder
            ->add('comentario','text',array(
                'label'=>'Mensaje',
                'required'=>true,
                'attr'=>array(
                    'class'=>'cleditor tinymce form-control placeholder',
                    'data-theme' => 'advanced',
                    'placeholder'=>'Mensaje',
                    )
                ))
            ->add('nivel','hidden')
            ->add('isAdmin','hidden')
            ->add($builder->create('foro', 'hidden')->addModelTransformer($foroTransformer))
            ->add($builder->create('usuario', 'hidden')->addModelTransformer($usuarioTransformer))
            ->add($builder->create('parent', 'hidden')->addModelTransformer($parentTransformer))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\FrontendBundle\Entity\Comentario'
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
        return 'richpolis_frontendbundle_comentario';
    }
}
