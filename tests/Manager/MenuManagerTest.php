<?php
/**
 * For licensing information, please see the LICENSE file accompanied with this file.
 *
 * @author Gerard van Helden <drm@melp.nl>
 * @copyright 2012 Gerard van Helden <http://melp.nl>
 */

namespace ZichtTest\Bundle\MenuBundle\Manager;

use Doctrine\ORM\EntityManager;
use Zicht\Bundle\MenuBundle\Manager\MenuManager;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;

class MenuManagerTest extends \PHPUnit_Framework_TestCase
{
    function testAddItem()
    {
        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->setMethods(array('getManager'))->getMock();
        $em = $this->getMockBuilder(EntityManager::class)->setMethods(array('persist', 'flush'))->getMock();
        $doctrine->expects($this->any())->method('getManager')->will($this->returnValue($em));

        $mgr = new MenuManager($doctrine);

        $items = array(
            new MenuItem(),
            new MenuItem(),
            new MenuItem()
        );

        $em->expects($this->exactly(count($items)))->method('persist');

        foreach ($items as $item) {
            $mgr->addItem($item);
        }
    }


    function testRemoveItem()
    {
        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->setMethods(array('getManager'))->getMock();
        $em = $this->getMockBuilder(EntityManager::class)->setMethods(array('remove', 'flush'))->getMock();
        $doctrine->expects($this->any())->method('getManager')->will($this->returnValue($em));

        $mgr = new MenuManager($doctrine);

        $items = array(
            new MenuItem(),
            new MenuItem(),
            new MenuItem()
        );

        $em->expects($this->exactly(count($items)))->method('remove');
        
        foreach ($items as $item) {
            $mgr->removeItem($item);
        }
    }
}