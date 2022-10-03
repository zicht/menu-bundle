<?php
/**
 * @copyright Zicht Online <https://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Security\Authorization;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;

class MenuVoter implements VoterInterface
{
    /**
     * The role name to use for having access to the admin menu
     */
    const ROLE_ADMIN_MENU_ITEM = 'ROLE_ADMIN_MENU_ITEM';

    /**
     * Whether the user has access to the 'name' field.
     */
    const ROLE_NAME_FIELD_ACCESS = 'ROLE_NAME_FIELD_ACCESS';

    /** @var RoleHierarchyInterface */
    protected $hierarchy;

    public function __construct(RoleHierarchyInterface $hierarchy)
    {
        $this->hierarchy = $hierarchy;
    }

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, ['DELETE', self::ROLE_NAME_FIELD_ACCESS]);
    }

    public function supportsClass($class)
    {
        return $class === MenuItem::class;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (false === $this->supportsAttribute($attribute)) {
                return self::ACCESS_ABSTAIN;
            }
        }

        if (!is_null($object) && $this->supportsClass(get_class($object))) {
            if ($this->userIsAllowed($token)) {
                return self::ACCESS_GRANTED;
            } else {
                // check for new objects or objects with name values
                if (is_null($object->getId()) || strlen($object->getName())) {
                    return self::ACCESS_DENIED;
                } else {
                    return self::ACCESS_GRANTED;
                }
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }

    /**
     * Calculate whether the current token is allowed.
     *
     * @return  bool
     */
    protected function userIsAllowed(TokenInterface $token)
    {
        /** @var \Symfony\Component\Security\Core\Role\RoleInterface $role */
        foreach ($this->hierarchy->getReachableRoles($token->getRoles()) as $role) {
            if ($role->getRole() === self::ROLE_ADMIN_MENU_ITEM) {
                return true;
            }
        }
        return false;
    }
}
