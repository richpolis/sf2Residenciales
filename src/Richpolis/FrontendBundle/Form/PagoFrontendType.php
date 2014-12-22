<?php

namespace Richpolis\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\BackendBundle\Form\DataTransformer\UsuarioToNumberTransformer;

class PagoFrontendType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	$em = $options['em'];
        $usuarioTransformer = new UsuarioToNumberTransformer($em);
        $builder
            ->add('isAproved','hidden')
            ->add('referencia',null,array('read_only'=>true,'label'=>'Referencia','attr'=>array('class'=>'form-control'))) 
            ->add('archivo','hidden')
            ->add('status','hidden')
            ->add('file','file',array(
                'label'=>'Comprobante',
                'attr'=>array('class'=>'form-control')
                ))
            ->add('monto','hidden')
            ->add($builder->create('usuario', 'hidden')->addModelTransformer($usuarioTransformer))
                
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\FrontendBundle\Entity\Pago'
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
        return 'richpolis_frontendbundle_pago';
    }
}
