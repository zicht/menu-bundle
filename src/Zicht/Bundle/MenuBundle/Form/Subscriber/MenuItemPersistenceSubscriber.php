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


    function __construct(MenuManager $mm, Provider $provider, FormBuilderInterface $builder)
    {
        $this->mm = $mm;
        $this->provider = $provider;
        $this->builder = $builder;
    }



    function postSetData(FormEvent $e)
    {
        var_dump('postSetData');

        if ($e->getForm()->getParent()->getData() === null) {
            return;
        }
        if ($this->provider->supports($e->getForm()->getParent()->getData())) {
            if ($item = $this->mm->getItem($this->provider->url($e->getForm()->getParent()->getData()))) {
                $item->setAddToMenu(true);
                $e->getForm()->getParent()->get($this->builder->getName())->setData($item);
            }
        }
    }


    function postSubmit(FormEvent $e)
    {
        var_dump('postSubmit');
        exit;

        if ($e->getForm()->getParent()->getRoot()->isValid()) {

            var_dump($e->getForm()->getParent()->getRoot()->isValid());
            exit;

            $menuItem = $e->getForm()->getParent()->get($this->builder->getName())->getData();
            if ($menuItem->isAddToMenu()) {
                if (!$menuItem->getTitle()) {
                    $menuItem->setTitle((string) $e->getForm()->getParent()->getData());
                }
                $menuItem->setPath($this->provider->url($e->getForm()->getParent()->getData(), array('aliasing' => false)));
                $this->mm->addItem($menuItem);
            } elseif ($menuItem->getId()) {
                $this->mm->removeItem($menuItem);
            }
        }
    }
}