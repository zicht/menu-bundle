<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Manager;

use \Doctrine\Bundle\DoctrineBundle\Registry;
use \Zicht\Bundle\MenuBundle\Entity\MenuItem;

/**
 * Class MenuManager
 * @package Zicht\Bundle\MenuBundle\Manager
 */
class MenuManager
{
    /**
     * Constructor.
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->items = array();
        $this->remove = array();
    }


    /**
     * Registers a menu item to add
     *
     * @param MenuItem $item
     * @return void
     */
    public function addItem(MenuItem $item)
    {
        $this->items[]= $item;
    }


    /**
     * Registers a menu item to remove
     *
     * @param MenuItem $item
     * @return void
     */
    public function removeItem(MenuItem $item)
    {
        $this->remove[]= $item;
    }


    /**
     * Registers a menu item to remove
     *
     * @param bool $flushEntityManager
     */
    public function flush($flushEntityManager = false)
    {
        $itemsToFlush = array();
        foreach ($this->items as $item) {
            $this->doctrine->getManager()->persist($item);
            if ($flushEntityManager) {
                $itemsToFlush[]= $item;
            }
        }
        foreach ($this->remove as $item) {
            $this->doctrine->getManager()->remove($item);
            if ($flushEntityManager) {
                $itemsToFlush[]= $item;
            }
        }

        if (count($itemsToFlush) > 0) {
            foreach ($itemsToFlush as $item) {
                $this->doctrine->getManager()->flush($item);
            }
        }
    }


    /**
     * Find an item by a path
     *
     * @param string $path
     * @return \Zicht\Bundle\MenuBundle\Entity\MenuItem
     * @deprecated Use getItemBy(array(':path' => $path))
     */
    public function getItem($path)
    {
        return $this->doctrine->getManager()->getRepository('ZichtMenuBundle:MenuItem')->findOneByPath($path);
    }


    /**
     * Finds an item in the menu repository by specific property (either 'name' or 'path')
     *
     * @param array $parameters Array containing keys ':name' or ':path'
     * @param MenuItem $ancestor Optional MenuItem whose descendants will be searched
     * @return null|\Zicht\Bundle\MenuBundle\Entity\MenuItem
     */
    public function getItemBy(array $parameters, MenuItem $ancestor = null)
    {
        $where = array();
        foreach ($parameters as $key=>$value) {
            switch ($key) {
                case ':name':
                    $where [] = 'm.name = :name';
                    break;
                case ':path':
                    $where [] = 'm.path = :path';
                    break;
                case ':language':
                    $where [] = 'm.language = :language';
                    break;
                default:
                    throw new \Exception("Unsupported parameter [$key].");
                    break;
            }
        }

        if (!is_null($ancestor)) {
            $where [] = 'm.lft > :lft';
            $where [] = 'm.rgt < :rgt';
            $parameters[':lft'] = $ancestor->getLft();
            $parameters[':rgt'] = $ancestor->getRgt();
        }

        /** @var \Doctrine\Orm\Query $query */
        $query = $this->doctrine->getManager()->createQuery(
            join(
                ' ',
                array(
                    'SELECT m FROM ZichtMenuBundle:MenuItem m WHERE',
                    join(' AND ', $where),
                    'ORDER BY m.lft',
                )
            )
        );
        $query->setParameters($parameters);
        $query->setMaxResults(1);

        $result = $query->getResult();
        if (empty($result)) {
            return null;
        }

        return current($result);
    }
}