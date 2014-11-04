<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Manager;

use \Doctrine\Bundle\DoctrineBundle\Registry;
use \Zicht\Bundle\MenuBundle\Entity\MenuItem;

class MenuManager
{
    /**
     * @param Registry $doctrine
     */
    function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->items = array();
        $this->remove = array();
    }


    /**
     * @param MenuItem $item
     */
    function addItem(\Zicht\Bundle\MenuBundle\Entity\MenuItem $item)
    {
        $this->items[]= $item;
    }


    /**
     * @param MenuItem $item
     */
    function removeItem(\Zicht\Bundle\MenuBundle\Entity\MenuItem $item)
    {
        $this->remove[]= $item;
    }


    /**
     * @param bool $flush
     */
    function flush($flush = false)
    {
        foreach ($this->items as $item) {
            $this->doctrine->getManager()->persist($item);
        }
        foreach ($this->remove as $item) {
            $this->doctrine->getManager()->remove($item);
        }
        if ($flush) {
            $this->doctrine->getManager()->flush();
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
     * @param array $parameters Array containing keys ':name' or ':path'
     * @param MenuItem $ancestor Optional MenuItem whose children will be searched
     * @return \Zicht\Bundle\MenuBundle\Entity\MenuItem
     */
    public function getItemBy(array $parameters, MenuItem $ancestor=null)
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
        $query = $this->doctrine->getManager()->createQuery(join(' ', array('SELECT m FROM ZichtMenuBundle:MenuItem m WHERE', join(' AND ', $where))));
        $query->setParameters($parameters);
        $query->setMaxResults(1);
        return current($query->getResult());
    }
}