<?php
/**
 * For licensing information, please see the LICENSE file accompanied with this file.
 *
 * @author Gerard van Helden <drm@melp.nl>
 * @copyright 2012 Gerard van Helden <http://melp.nl>
 */
namespace ZichtTest\Bundle\MenuBundle\Manager;

use Zicht\Bundle\MenuBundle\Form\Subscriber\MenuItemPersistenceSubscriber;

class MenuItemPersistenceSubscriberTest extends \PHPUnit_Framework_TestCase
{
    function testSubscribedEvents()
    {
        $mm = $this->getMockBuilder('Zicht\Bundle\MenuBundle\Manager\MenuManager')->disableOriginalConstructor()->getMock();
        $provider = $this->getMock('Zicht\Bundle\UrlBundle\Url\Provider');
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')->disableOriginalConstructor()->getMock();
        $e = new MenuItemPersistenceSubscriber($mm, $provider, $builder);

        foreach ($e->getSubscribedEvents() as $type => $method) {
            $this->assertTrue(is_callable(array($e, $method)));
        }
    }
}