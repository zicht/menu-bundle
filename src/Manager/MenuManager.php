<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;

/**
 * Class MenuManager
 *
 * @package Zicht\Bundle\MenuBundle\Manager
 */
class MenuManager
{
    /** @var int */
    const REMOVE = 0x01;
    /** @var int */
    const ADD = 0x02;
    /** @var \SplQueue  */
    private $queue;
    /** @var Registry  */
    protected $doctrine;

    /**
     * MenuManager constructor.
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->queue = new \SplQueue();
    }


    /**
     * Registers a menu item to add
     *
     * @param MenuItem $item
     * @return void
     */
    public function addItem(MenuItem $item)
    {
        $this->queue->enqueue([$item, self::ADD]);
    }


    /**
     * Registers a menu item to remove
     *
     * @param MenuItem $item
     * @return void
     */
    public function removeItem(MenuItem $item)
    {
        $this->queue->enqueue([$item, self::REMOVE]);
    }


    /**
     * Registers a menu item to remove
     *
     * @param bool $flushEntityManager
     */
    public function flush($flushEntityManager = false)
    {
        $this->queue->rewind();

        while (!$this->queue->isEmpty()) {
            list($item, $mode) = $this->queue->dequeue();
            switch ($mode) {
                case self::REMOVE:
                    if (($repo = $this->getRepository(get_class($item))) && $repo instanceof NestedTreeRepository) {
                        $repo->removeFromTree($item);
                    } else {
                        $this->doctrine->getManager()->remove($item);
                    }
                    break;
                case self::ADD:
                    $this->doctrine->getManager()->persist($item);
                    break;
            }
        }

        if ($flushEntityManager) {
            $this->doctrine->getManager()->flush();
        }
    }

    /**
     * @param string $className
     * @return ObjectRepository
     */
    private function getRepository($className)
    {
        if (null !== $manager = $this->doctrine->getManagerForClass($className)) {
            return $manager->getRepository($className);
        }

        return null;
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
     * @throws \Exception
     */
    public function getItemBy(array $parameters, MenuItem $ancestor = null)
    {
        $where = array();
        if (!is_null($ancestor)) {
            $where [] = 'm.lft > :lft';
            $where [] = 'm.rgt < :rgt';
            $parameters[':lft'] = $ancestor->getLft();
            $parameters[':rgt'] = $ancestor->getRgt();

            if (!isset($parameters[':language']) && $language = $ancestor->getLanguage()) {
                $parameters[':language']= $language;
            }
        }

        foreach ($parameters as $key => $value) {
            switch ($key) {
                case ':name':
                    $where []= 'm.name = :name';
                    break;
                case ':language':
                    $where []= '( m.language = :language OR ( m.language IS NULL AND root.language = :language) )';
                    break;
                case ':path':
                    $where []= 'm.path = :path';
                    break;
                case ':level':
                    $where [] = 'm.lvl = :level';
                case ':lft':
                case ':rgt':
                    break;
                default:
                    throw new \Exception("Unsupported parameter [$key].");
                    break;
            }
        }

        /** @var \Doctrine\Orm\Query $query */
        $query = $this->doctrine->getManager()->createQuery(
            join(
                ' ',
                array(
                    'SELECT m, root FROM ZichtMenuBundle:MenuItem m INNER JOIN ZichtMenuBundle:MenuItem root WITH m.root=root.id WHERE',
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
