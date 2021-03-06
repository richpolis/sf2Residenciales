<?php

namespace Richpolis\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Form\DataTransformer\RecursoToNumberTransformer;
use Richpolis\BackendBundle\Form\DataTransformer\UsuarioToNumberTransformer;

class ReservacionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $usuarioTransformer = new UsuarioToNumberTransformer($em);
        $recursoTransformer = new RecursoToNumberTransformer($em);
        $builder
            ->add('fechaEvento','date',array(
                'widget' => 'single_text', 
                'format' => 'yyyy-MM-dd',
                'attr'=>array('class'=>'form-control')
                ))
            ->add('desde','time',array(
                'widget' => 'single_text',
                'with_seconds'=> false,
                'attr'=>array('class'=>'form-control')
                ))
            ->add('hasta','time',array(
                'widget' => 'single_text',
                'with_seconds'=> false,
                'attr'=>array('class'=>'form-control')
                ))
            ->add('isAproved','hidden')
            ->add('monto','money',array(
                'currency'=>'MXN',
                'grouping'=>true,
                'read_only'=>true,
                'attr'=>array('class'=>'form-control')
                ))
            ->add('status','hidden')
            ->add($builder->create('recurso', 'hidden')->addModelTransformer($recursoTransformer))
            ->add($builder->create('usuario', 'hidden')->addModelTransformer($usuarioTransformer))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\FrontendBundle\Entity\Reservacion'
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
        return 'richpolis_frontendbundle_reservacion';
    }
}
