<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Security\Authorization;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Zicht\Bundle\MenuBundle\Admin\MenuItemAdmin;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;

/**
 * Class MenuVoter
 *
 * @package Zicht\Bundle\MenuBundle\Security\Authorization
 */
class MenuVoter implements VoterInterface
{
    const ROLE_ADMIN_MENU_ITEM = "ROLE_ADMIN_MENU_ITEM";
    const ROLE_NAME_FIELD_ACCESS = "ROLE_NAME_FIELD_ACCESS";

    /** @var RoleHierarchyInterface  */
    protected $hierarchy;

    /**
     * @inheritdoc
     */
    function __construct(RoleHierarchyInterface $hierarchy)
    {
        $this->hierarchy = $hierarchy;
    }

    /**
     * @inheritdoc
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array('DELETE', self::ROLE_NAME_FIELD_ACCESS));
    }

    /**
     * @inheritdoc
     */
    public function supportsClass($class)
    {
        return $class === MenuItem::class;
    }

    /**
     * @inheritdoc
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {

        foreach ($attributes as $attribute) {
            if (false === $this->supportsAttribute($attribute)) {
                return self::ACCESS_ABSTAIN;
            }
        }

        if ($this->supportsClass(get_class($object))) {
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
     * @param   TokenInterface $token
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