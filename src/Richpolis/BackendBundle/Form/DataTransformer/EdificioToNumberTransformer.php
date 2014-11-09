<?php 

namespace Richpolis\BackendBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Richpolis\BackendBundle\Entity\Edificio;

class EdificioToNumberTransformer implements DataTransformerInterface
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
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($edificio)
    {
        if (null === $edificio) {
            return "";
        }
        return $edificio->getId();
    }
    /**
     * Transforms a string (number) to an object (edificio).
     *
     * @param  string $number
     *
     * @return Comentario|null
     *
     * @throws TransformationFailedException if object (edificio) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }
        $edificio = $this->om
            ->getRepository('BackendBundle:Edificio')
            ->find($number);
        ;
        if (null === $edificio) {
            throw new TransformationFailedException(sprintf(
                'An Residencial with id "%s" does not exist!',
                $number
            ));
        }
        return $edificio;
    }
}