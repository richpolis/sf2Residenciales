<?php 

namespace Richpolis\BackendBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Richpolis\BackendBundle\Entity\Residencial;

class ResidencialToNumberTransformer implements DataTransformerInterface
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
    public function transform($residencial)
    {
        if (null === $residencial) {
            return "";
        }
        return $residencial->getId();
    }
    /**
     * Transforms a string (number) to an object (residencial).
     *
     * @param  string $number
     *
     * @return Comentario|null
     *
     * @throws TransformationFailedException if object (residencial) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }
        $residencial = $this->om
            ->getRepository('BackendBundle:Residencial')
            ->find($number);
        ;
        if (null === $residencial) {
            throw new TransformationFailedException(sprintf(
                'An Residencial with id "%s" does not exist!',
                $number
            ));
        }
        return $residencial;
    }
}