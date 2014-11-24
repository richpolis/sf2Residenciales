<?php 

namespace Richpolis\BackendBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;

class EdificiosToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }
    
    /**
     * Transforms an ArrayCollection en Array.
     *
     * @param  ArrayCollection
     * @return array
     */
    public function transform($edificios)
    {
        $arreglo = array();
        foreach($edificios as $edificio){
            $arreglo[] = $edificio->getId();
        }
        return $arreglo;
    }
    
    /**
     * Transforms un arreglo en un ArrayCollection.
     *
     * @param  array
     *
     * @return ArrayCollection
     */
    public function reverseTransform($arreglo)
    {
        if (count($arreglo)==0) {
            return null;
        }
        $largo = count($arreglo)-1;
        $edificios = new \Doctrine\Common\Collections\ArrayCollection();
        for($cont = 0; $cont < $largo; $cont++){
            $id = $arreglo[$cont];
            $edificio = $this->om
                ->getRepository('BackendBundle:Edificio')
                ->find($id);
            if (null !== $edificio) {
                $edificios->add($edificio);
            }
        }
        return $edificios;
    }
}