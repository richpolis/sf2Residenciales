<?php 

namespace Richpolis\BackendBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Richpolis\BackendBundle\Entity\Recurso;

class RecursoToNumberTransformer implements DataTransformerInterface
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
    public function transform($recurso)
    {
        if (null === $recurso) {
            return "";
        }
        return $recurso->getId();
    }
    /**
     * Transforms a string (number) to an object (recurso).
     *
     * @param  string $number
     *
     * @return Comentario|null
     *
     * @throws TransformationFailedException if object (recurso) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }
        $recurso = $this->om
            ->getRepository('BackendBundle:Recurso')
            ->find($number);
        ;
        if (null === $recurso) {
            throw new TransformationFailedException(sprintf(
                'An Residencial with id "%s" does not exist!',
                $number
            ));
        }
        return $recurso;
    }
}