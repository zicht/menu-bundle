<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace ZichtTest\Bundle\MenuBundle\Manager;

use PHPUnit\Framework\TestCase;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;
use Zicht\Bundle\MenuBundle\Manager\MenuManager;

class MenuManagerTest extends TestCase
{
    public function testAddItem()
    {
        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->setMethods(['getManager'])->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->setMethods(['persist', 'flush'])->disableOriginalConstructor()->getMock();
        $doctrine->expects($this->any())->method('getManager')->will($this->returnValue($em));

        $mgr = new MenuManager($doctrine);

        $items = [
            new MenuItem(),
            new MenuItem(),
            new MenuItem(),
        ];

        $em->expects($this->exactly(count($items)))->method('persist');
        $em->expects($this->once())->method('flush');

        foreach ($items as $item) {
            $mgr->addItem($item);
        }
        $mgr->flush(true);
    }

    public function testRemoveItem()
    {
        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')->disableOriginalConstructor()->setMethods(['getManager', 'getManagerForClass'])->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->setMethods(['remove', 'flush'])->disableOriginalConstructor()->getMock();
        $doctrine->expects($this->any())->method('getManager')->will($this->returnValue($em));
        $doctrine->expects($this->any())->method('getManagerForClass')->will($this->returnValue(null));

        $mgr = new MenuManager($doctrine);

        $items = [
            new MenuItem(),
            new MenuItem(),
            new MenuItem(),
        ];

        $em->expects($this->exactly(count($items)))->method('remove');
        $em->expects($this->once())->method('flush');

        foreach ($items as $item) {
            $mgr->removeItem($item);
        }
        $mgr->flush(true);
    }
}
