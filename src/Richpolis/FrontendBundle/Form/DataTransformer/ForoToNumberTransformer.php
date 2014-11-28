<?php 

namespace Richpolis\FrontendBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Richpolis\FrontendBundle\Entity\Foro;

class ForoToNumberTransformer implements DataTransformerInterface
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
    public function transform($foro)
    {
        if (null === $foro) {
            return "";
        }
        return $foro->getId();
    }
    /**
     * Transforms a string (number) to an object (comentario).
     *
     * @param  string $number
     *
     * @return Foro|null
     *
     * @throws TransformationFailedException if object (comentario) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }
        $foro = $this->om
            ->getRepository('FrontendBundle:Foro')
            ->find($number);
        ;
        if (null === $foro) {
            throw new TransformationFailedException(sprintf(
                'An Foro with id "%s" does not exist!',
                $number
            ));
        }
        return $foro;
    }
}
