<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Form\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Zicht\Bundle\MenuBundle\Manager\MenuManager;
use Zicht\Bundle\UrlBundle\Url\Provider;

class MenuItemPersistenceSubscriber implements EventSubscriberInterface
{
    /**
     * @param string $property
     */
    public function __construct(MenuManager $mm, Provider $provider, $property)
    {
        $this->mm = $mm;
        $this->provider = $provider;
        $this->property = $property;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    public function postSetData(FormEvent $e): void
    {
        if ($e->getData() === null) {
            return;
        }

        // Checks if the form has a given property
        // A property can be removed in a child class but the eventSubscriber still exists
        if (!$e->getForm()->has($this->property)) {
            return;
        }
        if ($this->provider->supports($e->getData())) {
            if ($item = $this->mm->getItemBy([':path' => $this->provider->url($e->getData())])) {
                $item->setAddToMenu(true);
                $e->getForm()->get($this->property)->setData($item);
            }
        }
    }

    public function postSubmit(FormEvent $e): void
    {
        if ($e->getForm()->has($this->property) && $e->getForm()->getRoot()->isValid()) {
            $menuItem = $e->getForm()->get($this->property)->getData();

            if ($menuItem->isAddToMenu()) {
                if (!$menuItem->getTitle()) {
                    $menuItem->setTitle((string)$e->getData());
                }
                $menuItem->setPath($this->provider->url($e->getData(), ['aliasing' => false]));
                $this->mm->addItem($menuItem);
            } elseif ($menuItem->getId()) {
                $this->mm->removeItem($menuItem);
            }
        }
    }
}
