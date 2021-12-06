<?php
/**
 * For licensing information, please see the LICENSE file accompanied with this file.
 *
 * @author Gerard van Helden <drm@melp.nl>
 * @copyright 2012 Gerard van Helden <http://melp.nl>
 */

namespace ZichtTest\Bundle\MenuBundle\Manager;

use PHPUnit\Framework\TestCase;
use Zicht\Bundle\MenuBundle\Manager\MenuManager;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;

class MenuManagerTest extends TestCase
{
    function testAddItem()
    {
        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->setMethods(array('getManager'))->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->setMethods(array('persist', 'flush'))->disableOriginalConstructor()->getMock();
        $doctrine->expects($this->any())->method('getManager')->will($this->returnValue($em));

        $mgr = new MenuManager($doctrine);

        $items = array(
            new MenuItem(),
            new MenuItem(),
            new MenuItem()
        );

        $em->expects($this->exactly(count($items)))->method('persist');
        $em->expects($this->once())->method('flush');

        foreach ($items as $item) {
            $mgr->addItem($item);
        }
        $mgr->flush(true);
    }


    function testRemoveItem()
    {
        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->setMethods(array('getManager', 'getManagerForClass'))->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->setMethods(array('remove', 'flush'))->disableOriginalConstructor()->getMock();
        $doctrine->expects($this->any())->method('getManager')->will($this->returnValue($em));
        $doctrine->expects($this->any())->method('getManagerForClass')->will($this->returnValue(null));

        $mgr = new MenuManager($doctrine);

        $items = array(
            new MenuItem(),
            new MenuItem(),
            new MenuItem()
        );

        $em->expects($this->exactly(count($items)))->method('remove');
        $em->expects($this->once())->method('flush');
        
        foreach ($items as $item) {
            $mgr->removeItem($item);
        }
        $mgr->flush(true);
    }
}