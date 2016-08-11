<?php
/**
 * @author Muhammed Akbulut <muhammed@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Validator;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;

/**
 * Class ParentValidator
 *
 * @package Zicht\Bundle\MenuBundle\Validator
 */
class ParentValidator
{
    /**
     * Validates parents of a MenuItem
     *
     * @param MenuItem $object
     * @param ExecutionContextInterface $context
     */
    public static function validate(MenuItem $object, ExecutionContextInterface $context)
    {
        $tempObject = $object->getParent();

        while ($tempObject !== null) {
            if ($tempObject === $object) {
                $context->buildViolation(
                    'Circular reference error. '
                    . 'An object can not reference with a parent to itself nor to an ancestor of itself'
                )
                    ->atPath('parent')
                    ->addViolation();

                break;
            }

            $tempObject = $tempObject->getParent();
        }
    }
}
