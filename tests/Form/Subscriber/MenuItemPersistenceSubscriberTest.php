<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace ZichtTest\Bundle\MenuBundle\Form\Subscriber;

use PHPUnit\Framework\TestCase;
use Zicht\Bundle\MenuBundle\Form\Subscriber\MenuItemPersistenceSubscriber;

class MenuItemPersistenceSubscriberTest extends TestCase
{
    public function testSubscribedEvents()
    {
        $mm = $this->getMockBuilder('Zicht\Bundle\MenuBundle\Manager\MenuManager')->disableOriginalConstructor()->getMock();
        $provider = $this->createMock('Zicht\Bundle\UrlBundle\Url\Provider');
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')->disableOriginalConstructor()->getMock();
        $e = new MenuItemPersistenceSubscriber($mm, $provider, $builder);

        foreach ($e->getSubscribedEvents() as $type => $method) {
            $this->assertTrue(is_callable([$e, $method]));
        }
    }
}
