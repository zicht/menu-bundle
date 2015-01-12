<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Form\Subscriber;

use \Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \Symfony\Component\Form\FormEvents;
use \Symfony\Component\Form\FormEvent;
use \Symfony\Component\Form\FormBuilderInterface;

use \Zicht\Bundle\MenuBundle\Manager\MenuManager;
use \Zicht\Bundle\UrlBundle\Url\Provider;

/**
 * Class MenuItemPersistenceSubscriber
 *
 * @package Zicht\Bundle\MenuBundle\Form\Subscriber
 */
class MenuItemPersistenceSubscriber implements EventSubscriberInterface
{
    /**
     * @{inheritDoc}
     */
    static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA   => 'postSetData',
            FormEvents::POST_SUBMIT     => 'postSubmit'
        );
    }

    /**
     * Constructor
     *
     * @param MenuManager $mm
     * @param Provider $provider
     * @param string $property
     */
    function __construct(MenuManager $mm, Provider $provider, $property)
    {
        $this->mm = $mm;
        $this->provider = $provider;
        $this->property = $property;
    }

    /**
     * POST_SET_DATA event handler
     *
     * @param FormEvent $e
     * @return void
     */
    function postSetData(FormEvent $e)
    {
        if ($e->getData() === null) {
            return;
        }
        if ($this->provider->supports($e->getData())) {
            if ($item = $this->mm->getItem($this->provider->url($e->getData()))) {
                $item->setAddToMenu(true);
                $e->getForm()->get($this->property)->setData($item);
            }
        }
    }

    /**
     * POST_SUBMIT handler
     *
     * @param FormEvent $e
     * @return void
     */
    function postSubmit(FormEvent $e)
    {
        if ($e->getForm()->getRoot()->isValid()) {
            $menuItem = $e->getForm()->get($this->property)->getData();
            if ($menuItem->isAddToMenu()) {
                if (!$menuItem->getTitle()) {
                    $menuItem->setTitle((string)$e->getData());
                }
                $menuItem->setPath($this->provider->url($e->getData(), array('aliasing' => false)));
                $this->mm->addItem($menuItem);
            } elseif ($menuItem->getId()) {
                $this->mm->removeItem($menuItem);
            }
        }
    }
}